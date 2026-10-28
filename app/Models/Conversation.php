<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'user_one_id',
        'user_two_id',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function getOtherUser(int|User $user): ?User
    {
        $userId = $user instanceof User ? $user->id : $user;
        if ($this->user_one_id === $userId) {
            return $this->userTwo;
        }
        return $this->userOne;
    }

    public function hasParticipant(int|User $user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;
        return $this->user_one_id === $userId || $this->user_two_id === $userId;
    }

    public static function findOrCreateBetween(int $workspaceId, int $userIdA, int $userIdB): self
    {
        $userOne = min($userIdA, $userIdB);
        $userTwo = max($userIdA, $userIdB);

        return self::firstOrCreate([
            'workspace_id' => $workspaceId,
            'user_one_id' => $userOne,
            'user_two_id' => $userTwo,
        ]);
    }
}
