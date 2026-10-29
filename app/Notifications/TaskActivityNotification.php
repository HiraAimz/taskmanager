<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskActivityNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Task $task,
        public string $eventType,
        public string $subjectLine,
        public string $messageText,
        public ?User $actor = null,
        public array $details = []
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $project = $this->task->project;
        $workspace = $project?->workspace;
        $taskUrl = url("/projects/{$project?->id}/board?task={$this->task->id}");

        return (new MailMessage)
            ->subject($this->subjectLine)
            ->view('emails.task-activity', [
                'recipient' => $notifiable,
                'task' => $this->task,
                'project' => $project,
                'workspace' => $workspace,
                'actor' => $this->actor,
                'eventType' => $this->eventType,
                'subjectLine' => $this->subjectLine,
                'messageText' => $this->messageText,
                'details' => $this->details,
                'taskUrl' => $taskUrl,
            ]);
    }
}
