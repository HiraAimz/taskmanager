<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Attachment;
use App\Models\Comment;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskActivityNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Models\Activity;

class TaskActivityService
{
    /**
     * Map internal activity event names to user notification preference keys.
     */
    protected const EVENT_PREFERENCE_MAP = [
        'task_created' => 'task_assigned',
        'task_assigned' => 'task_assigned',
        'task_reassigned' => 'task_assigned',
        'task_unassigned' => 'task_assigned',
        'collaborator_added' => 'task_assigned',
        'collaborator_removed' => 'task_assigned',
        'task_status_changed' => 'task_status_changed',
        'task_completed' => 'task_status_changed',
        'task_reopened' => 'task_status_changed',
        'task_comment_added' => 'task_comment_added',
        'task_due_date_changed' => 'task_due_date_changed',
        'task_priority_changed' => 'task_priority_changed',
        'task_updated' => 'task_updated',
        'attachment_added' => 'task_updated',
        'attachment_removed' => 'task_updated',
        'task_comment_updated' => 'task_updated',
        'task_comment_deleted' => 'task_updated',
    ];

    /**
     * Log a task activity and safely dispatch email notifications to eligible participants.
     */
    public function logAndNotify(
        Task $task,
        string $event,
        string $description,
        ?array $changes = null,
        ?User $actor = null,
        array $extra = []
    ): Activity {
        $actor ??= auth()->user();

        // Ensure task has project and workspace relationships loaded for metadata
        $task->loadMissing('project.workspace', 'assignee', 'creator');

        $properties = array_merge([
            'event_type' => $event,
            'project_id' => $task->project_id,
            'workspace_id' => $task->project?->workspace_id,
        ], $extra);

        if ($changes !== null) {
            $properties = array_merge($properties, $changes);
        }

        // 1. Record activity in database
        $activity = activity()
            ->performedOn($task)
            ->causedBy($actor)
            ->event($event)
            ->withProperties($properties)
            ->log($description);

        // 2. Dispatch notifications safely
        $this->dispatchNotifications($task, $event, $description, $changes, $actor, $extra);

        return $activity;
    }

    /**
     * Helper to log when a task is created.
     */
    public function logTaskCreated(Task $task, ?User $actor = null): Activity
    {
        $actor ??= auth()->user();

        return $this->logAndNotify(
            task: $task,
            event: 'task_created',
            description: 'created this task',
            changes: [
                'field_name' => 'Task',
                'new' => $task->title,
            ],
            actor: $actor,
            extra: [
                'title' => $task->title,
                'status' => $task->status?->name,
                'priority' => $task->priority?->value ?? (string) $task->priority,
            ]
        );
    }

    /**
     * Helper to log status change.
     */
    public function logStatusChange(Task $task, Status $oldStatus, Status $newStatus, ?User $actor = null): Activity
    {
        $actor ??= auth()->user();

        $event = 'task_status_changed';
        $desc = "changed status from {$oldStatus->name} to {$newStatus->name}";

        $isOldDone = ($oldStatus->type === \App\Enums\StatusType::Done) || (is_string($oldStatus->type) && $oldStatus->type === 'done') || strtolower($oldStatus->name) === 'done';
        $isNewDone = ($newStatus->type === \App\Enums\StatusType::Done) || (is_string($newStatus->type) && $newStatus->type === 'done') || strtolower($newStatus->name) === 'done';

        if ($isNewDone && ! $isOldDone) {
            $event = 'task_completed';
            $desc = "completed this task (status: {$newStatus->name})";
        } elseif (! $isNewDone && $isOldDone) {
            $event = 'task_reopened';
            $desc = "reopened this task (status: {$newStatus->name})";
        }

        return $this->logAndNotify(
            task: $task,
            event: $event,
            description: $desc,
            changes: [
                'field_name' => 'Status',
                'old' => $oldStatus->name,
                'new' => $newStatus->name,
                'old_status_id' => $oldStatus->id,
                'new_status_id' => $newStatus->id,
            ],
            actor: $actor
        );
    }

    /**
     * Helper to log assignee change or reassignment.
     */
    public function logAssigneeChange(Task $task, ?User $oldAssignee, ?User $newAssignee, ?User $actor = null): Activity
    {
        $actor ??= auth()->user();

        if ($oldAssignee === null && $newAssignee !== null) {
            $event = 'task_assigned';
            $desc = "assigned this task to {$newAssignee->name}";
        } elseif ($oldAssignee !== null && $newAssignee === null) {
            $event = 'task_unassigned';
            $desc = "unassigned this task (was {$oldAssignee->name})";
        } else {
            $event = 'task_reassigned';
            $desc = "reassigned task from {$oldAssignee?->name} to {$newAssignee?->name}";
        }

        return $this->logAndNotify(
            task: $task,
            event: $event,
            description: $desc,
            changes: [
                'field_name' => 'Assignee',
                'old' => $oldAssignee?->name,
                'new' => $newAssignee?->name,
                'old_assignee_id' => $oldAssignee?->id,
                'new_assignee_id' => $newAssignee?->id,
            ],
            actor: $actor,
            extra: [
                'previous_assignee_id' => $oldAssignee?->id,
                'new_assignee_id' => $newAssignee?->id,
            ]
        );
    }

    /**
     * Helper to log priority change.
     */
    public function logPriorityChange(Task $task, string $oldPriority, string $newPriority, ?User $actor = null): Activity
    {
        $actor ??= auth()->user();

        return $this->logAndNotify(
            task: $task,
            event: 'task_priority_changed',
            description: "changed priority from {$oldPriority} to {$newPriority}",
            changes: [
                'field_name' => 'Priority',
                'old' => ucfirst($oldPriority),
                'new' => ucfirst($newPriority),
            ],
            actor: $actor
        );
    }

    /**
     * Helper to log due date change.
     */
    public function logDueDateChange(Task $task, ?string $oldDueDate, ?string $newDueDate, ?User $actor = null): Activity
    {
        $actor ??= auth()->user();

        $desc = match (true) {
            $oldDueDate === null && $newDueDate !== null => "set due date to {$newDueDate}",
            $oldDueDate !== null && $newDueDate === null => "removed due date (was {$oldDueDate})",
            default => "changed due date from {$oldDueDate} to {$newDueDate}",
        };

        return $this->logAndNotify(
            task: $task,
            event: 'task_due_date_changed',
            description: $desc,
            changes: [
                'field_name' => 'Due Date',
                'old' => $oldDueDate,
                'new' => $newDueDate,
            ],
            actor: $actor
        );
    }

    /**
     * Helper to log comment added.
     */
    public function logCommentAdded(Task $task, Comment $comment, ?User $actor = null): Activity
    {
        $actor ??= auth()->user();

        return $this->logAndNotify(
            task: $task,
            event: 'task_comment_added',
            description: 'added a comment',
            changes: null,
            actor: $actor,
            extra: [
                'comment_id' => $comment->id,
                'comment_body' => $comment->body,
            ]
        );
    }

    /**
     * Helper to log comment updated.
     */
    public function logCommentUpdated(Task $task, Comment $comment, ?User $actor = null): Activity
    {
        $actor ??= auth()->user();

        return $this->logAndNotify(
            task: $task,
            event: 'task_comment_updated',
            description: 'updated a comment',
            changes: null,
            actor: $actor,
            extra: [
                'comment_id' => $comment->id,
            ]
        );
    }

    /**
     * Helper to log comment deleted.
     */
    public function logCommentDeleted(Task $task, ?User $actor = null): Activity
    {
        $actor ??= auth()->user();

        return $this->logAndNotify(
            task: $task,
            event: 'task_comment_deleted',
            description: 'deleted a comment',
            changes: null,
            actor: $actor
        );
    }

    /**
     * Helper to log attachment added.
     */
    public function logAttachmentAdded(Task $task, Attachment $attachment, ?User $actor = null): Activity
    {
        $actor ??= auth()->user();

        $fileName = $attachment->original_name ?? $attachment->file_name ?? 'file';

        return $this->logAndNotify(
            task: $task,
            event: 'attachment_added',
            description: "attached file '{$fileName}'",
            changes: [
                'field_name' => 'Attachment',
                'new' => $fileName,
            ],
            actor: $actor,
            extra: [
                'attachment_id' => $attachment->id,
                'file_name' => $fileName,
            ]
        );
    }

    /**
     * Helper to log attachment removed.
     */
    public function logAttachmentRemoved(Task $task, string $fileName, ?User $actor = null): Activity
    {
        $actor ??= auth()->user();

        return $this->logAndNotify(
            task: $task,
            event: 'attachment_removed',
            description: "removed attachment '{$fileName}'",
            changes: [
                'field_name' => 'Attachment',
                'old' => $fileName,
            ],
            actor: $actor,
            extra: [
                'file_name' => $fileName,
            ]
        );
    }

    /**
     * Helper to log collaborator added.
     */
    public function logCollaboratorAdded(Task $task, User $collaborator, ?User $actor = null): Activity
    {
        $actor ??= auth()->user();

        return $this->logAndNotify(
            task: $task,
            event: 'collaborator_added',
            description: "added {$collaborator->name} as a collaborator",
            changes: [
                'field_name' => 'Collaborator',
                'new' => $collaborator->name,
            ],
            actor: $actor,
            extra: [
                'collaborator_id' => $collaborator->id,
                'collaborator_name' => $collaborator->name,
            ]
        );
    }

    /**
     * Helper to log collaborator removed.
     */
    public function logCollaboratorRemoved(Task $task, User $collaborator, ?User $actor = null): Activity
    {
        $actor ??= auth()->user();

        return $this->logAndNotify(
            task: $task,
            event: 'collaborator_removed',
            description: "removed {$collaborator->name} as a collaborator",
            changes: [
                'field_name' => 'Collaborator',
                'old' => $collaborator->name,
            ],
            actor: $actor,
            extra: [
                'collaborator_id' => $collaborator->id,
                'collaborator_name' => $collaborator->name,
            ]
        );
    }

    /**
     * Safely determine recipients and dispatch email notifications without breaking the transaction.
     */
    protected function dispatchNotifications(
        Task $task,
        string $event,
        string $description,
        ?array $changes,
        ?User $actor,
        array $extra
    ): void {
        try {
            $recipients = $this->resolveRecipients($task, $event, $actor, $extra);

            if ($recipients->isEmpty()) {
                return;
            }

            $actorName = $actor?->name ?? 'A team member';
            $subjectLine = $this->buildSubjectLine($task, $event, $actorName);
            $messageText = "{$actorName} {$description} on task '{$task->title}'.";

            $details = $changes ?? [];
            if (! empty($extra['comment_body'])) {
                $details['comment_body'] = $extra['comment_body'];
            }

            foreach ($recipients as $recipient) {
                try {
                    $recipient->notify(new TaskActivityNotification(
                        task: $task,
                        eventType: $event,
                        subjectLine: $subjectLine,
                        messageText: $messageText,
                        actor: $actor,
                        details: $details
                    ));
                } catch (\Throwable $e) {
                    Log::error("Failed to send task activity notification to user #{$recipient->id}: " . $e->getMessage(), [
                        'exception' => $e,
                        'task_id' => $task->id,
                        'event' => $event,
                        'recipient_id' => $recipient->id,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Guarantee failure isolation so email issues NEVER break task operations
            Log::error("Failed in dispatchNotifications for task #{$task->id}: " . $e->getMessage(), [
                'exception' => $e,
                'task_id' => $task->id,
                'event' => $event,
            ]);
        }
    }

    /**
     * Determine eligible users who should receive an email for this event.
     * Enforces:
     * 1. Actor is never notified of their own action.
     * 2. Recipient must belong to the workspace.
     * 3. Recipient's notification preferences must allow this event type.
     */
    protected function resolveRecipients(Task $task, string $event, ?User $actor, array $extra): Collection
    {
        $task->loadMissing('project.workspace.users', 'assignee', 'assignees', 'creator');

        $workspace = $task->project?->workspace;
        if (! $workspace) {
            return collect();
        }

        $workspaceUserIds = $workspace->users->pluck('id')->all();
        $actorId = $actor?->id;
        $preferenceKey = self::EVENT_PREFERENCE_MAP[$event] ?? 'task_updated';

        $candidates = collect();

        switch ($event) {
            case 'task_created':
            case 'task_assigned':
                if (! empty($extra['new_assignee_id'])) {
                    $newA = User::find($extra['new_assignee_id']);
                    if ($newA) {
                        $candidates->push($newA);
                    }
                } elseif ($task->assignee) {
                    $candidates->push($task->assignee);
                }
                break;

            case 'task_reassigned':
                if (! empty($extra['new_assignee_id'])) {
                    $newA = User::find($extra['new_assignee_id']);
                    if ($newA) {
                        $candidates->push($newA);
                    }
                } elseif ($task->assignee) {
                    $candidates->push($task->assignee);
                }

                if (! empty($extra['previous_assignee_id'])) {
                    $prev = User::find($extra['previous_assignee_id']);
                    if ($prev) {
                        $candidates->push($prev);
                    }
                }
                break;

            case 'task_unassigned':
                if (! empty($extra['previous_assignee_id'])) {
                    $prev = User::find($extra['previous_assignee_id']);
                    if ($prev) {
                        $candidates->push($prev);
                    }
                }
                break;

            case 'collaborator_added':
                if (! empty($extra['collaborator_id'])) {
                    $collab = User::find($extra['collaborator_id']);
                    if ($collab) {
                        $candidates->push($collab);
                    }
                }
                break;

            case 'task_status_changed':
            case 'task_completed':
            case 'task_reopened':
            case 'task_comment_added':
                if ($task->assignee) {
                    $candidates->push($task->assignee);
                }
                if ($task->creator) {
                    $candidates->push($task->creator);
                }
                // Include any collaborators
                if ($task->assignees) {
                    foreach ($task->assignees as $collab) {
                        $candidates->push($collab);
                    }
                }
                break;

            case 'task_due_date_changed':
            case 'task_priority_changed':
            case 'task_updated':
            case 'attachment_added':
            case 'attachment_removed':
            default:
                if ($task->assignee) {
                    $candidates->push($task->assignee);
                }
                break;
        }

        return $candidates
            ->unique('id')
            ->filter(function (User $recipient) use ($actorId, $workspaceUserIds, $preferenceKey) {
                // Rule 1: Never notify actor
                if ($recipient->id === $actorId) {
                    return false;
                }

                // Rule 2: Must be workspace member
                if (! in_array($recipient->id, $workspaceUserIds, true)) {
                    return false;
                }

                // Rule 3: Check notification preference
                return $recipient->wantsNotification($preferenceKey);
            })
            ->values();
    }

    /**
     * Build an informative email subject line.
     */
    protected function buildSubjectLine(Task $task, string $event, string $actorName): string
    {
        $taskTitle = \Illuminate\Support\Str::limit($task->title, 40);

        return match ($event) {
            'task_created' => "[Task Created] {$taskTitle}",
            'task_assigned' => "[Assigned] You were assigned to: {$taskTitle}",
            'task_reassigned' => "[Reassigned] Task assignment updated: {$taskTitle}",
            'task_unassigned' => "[Unassigned] Task assignment removed: {$taskTitle}",
            'collaborator_added' => "[Collaborator] You were added to: {$taskTitle}",
            'task_completed' => "[Completed] {$actorName} completed: {$taskTitle}",
            'task_reopened' => "[Reopened] {$actorName} reopened: {$taskTitle}",
            'task_status_changed' => "[Status Updated] {$taskTitle}",
            'task_comment_added' => "[New Comment] {$actorName} on: {$taskTitle}",
            'task_due_date_changed' => "[Due Date Changed] {$taskTitle}",
            'task_priority_changed' => "[Priority Changed] {$taskTitle}",
            'attachment_added' => "[Attachment Added] {$taskTitle}",
            'attachment_removed' => "[Attachment Removed] {$taskTitle}",
            default => "[Updated] Activity on: {$taskTitle}",
        };
    }
}
