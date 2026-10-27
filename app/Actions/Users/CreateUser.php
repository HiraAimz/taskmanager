<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CreateUser
{
    /**
     * Create a new user with the given data.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws ValidationException
     */
    public function handle(array $data): User
    {
        // Validate required fields
        if (empty($data['name']) || empty($data['email']) || empty($data['password']) || empty($data['role'])) {
            throw ValidationException::withMessages([
                'data' => 'Name, email, password, and role are required.',
            ]);
        }

        // Create the user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Assign the role
        $user->assignRole($data['role']);

        return $user;
    }
}
