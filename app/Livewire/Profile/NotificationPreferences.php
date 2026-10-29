<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Models\UserNotificationPreference;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class NotificationPreferences extends Component
{
    public bool $task_assigned = true;

    public bool $task_status_changed = true;

    public bool $task_comment_added = true;

    public bool $task_due_date_changed = true;

    public bool $task_priority_changed = true;

    public bool $task_updated = true;

    public ?string $errorMessage = null;

    public function mount(): void
    {
        $prefs = UserNotificationPreference::forUser(auth()->id());

        $this->task_assigned = (bool) $prefs->task_assigned;
        $this->task_status_changed = (bool) $prefs->task_status_changed;
        $this->task_comment_added = (bool) $prefs->task_comment_added;
        $this->task_due_date_changed = (bool) $prefs->task_due_date_changed;
        $this->task_priority_changed = (bool) $prefs->task_priority_changed;
        $this->task_updated = (bool) $prefs->task_updated;
    }

    public function toggle(string $field): void
    {
        if (property_exists($this, $field) && is_bool($this->{$field})) {
            $this->{$field} = ! $this->{$field};
            $this->save();
        }
    }

    public function updated($propertyName): void
    {
        $this->save();
    }

    public function save(): void
    {
        $this->errorMessage = null;

        try {
            $prefs = UserNotificationPreference::forUser(auth()->id());
            $prefs->update([
                'task_assigned' => $this->task_assigned,
                'task_status_changed' => $this->task_status_changed,
                'task_comment_added' => $this->task_comment_added,
                'task_due_date_changed' => $this->task_due_date_changed,
                'task_priority_changed' => $this->task_priority_changed,
                'task_updated' => $this->task_updated,
            ]);

            session()->flash('message', 'Notification preferences saved successfully.');
        } catch (\Throwable $e) {
            Log::error('Failed to save notification preferences: ' . $e->getMessage());
            $this->errorMessage = 'Failed to save changes. Please try again.';
        }
    }

    public function render()
    {
        return view('livewire.profile.notification-preferences');
    }
}
