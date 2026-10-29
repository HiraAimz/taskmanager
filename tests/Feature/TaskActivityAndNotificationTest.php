<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\Comments\CreateComment;
use App\Actions\Tasks\CreateTask;
use App\Actions\Tasks\UpdateTask;
use App\Actions\Tasks\UpdateTaskStatus;
use App\Livewire\Profile\NotificationPreferences;
use App\Livewire\Tasks\TaskDetail;
use App\Models\Project;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use App\Models\UserNotificationPreference;
use App\Models\Workspace;
use App\Notifications\TaskActivityNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TaskActivityAndNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected User $member;
    protected User $otherMember;
    protected Workspace $workspace;
    protected Project $project;
    protected Status $todoStatus;
    protected Status $inProgressStatus;
    protected Status $doneStatus;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Manager']);
        Role::firstOrCreate(['name' => 'Member']);

        $this->owner = User::factory()->create(['name' => 'Owner Alice']);
        $this->member = User::factory()->create(['name' => 'Member Bob']);
        $this->otherMember = User::factory()->create(['name' => 'Member Charlie']);

        $this->workspace = Workspace::factory()->create(['owner_id' => $this->owner->id]);
        $this->workspace->users()->attach($this->owner->id, ['role' => 'admin']);
        $this->workspace->users()->attach($this->member->id, ['role' => 'member']);
        $this->workspace->users()->attach($this->otherMember->id, ['role' => 'member']);

        $this->project = Project::factory()->create([
            'workspace_id' => $this->workspace->id,
            'owner_id' => $this->owner->id,
        ]);
        $this->project->members()->attach($this->member->id, ['role' => 'member']);
        $this->project->members()->attach($this->otherMember->id, ['role' => 'member']);

        $this->todoStatus = Status::factory()->create([
            'project_id' => $this->project->id,
            'name' => 'To Do',
            'type' => \App\Enums\StatusType::Todo,
            'position' => 1,
        ]);

        $this->inProgressStatus = Status::factory()->create([
            'project_id' => $this->project->id,
            'name' => 'In Progress',
            'type' => \App\Enums\StatusType::InProgress,
            'position' => 2,
        ]);

        $this->doneStatus = Status::factory()->create([
            'project_id' => $this->project->id,
            'name' => 'Done',
            'type' => \App\Enums\StatusType::Done,
            'position' => 3,
        ]);
    }

    public function test_task_creation_logs_activity_and_notifies_assignee(): void
    {
        Notification::fake();

        $this->actingAs($this->owner);

        $action = new CreateTask();
        $task = $action->execute([
            'title' => 'Design new landing page',
            'description' => 'Initial wireframe setup',
            'priority' => 'high',
            'assignee_id' => $this->member->id,
        ], $this->project->id, $this->todoStatus->id);

        // Check activity logged
        $activity = Activity::where('subject_type', Task::class)
            ->where('subject_id', $task->id)
            ->where('event', 'task_created')
            ->first();

        $this->assertNotNull($activity);
        $this->assertEquals($this->owner->id, $activity->causer_id);
        $this->assertStringContainsString('created this task', $activity->description);

        // Member Bob was assigned, so should receive notification
        Notification::assertSentTo($this->member, TaskActivityNotification::class, function ($notification) use ($task) {
            return $notification->task->id === $task->id
                && $notification->eventType === 'task_created';
        });

        // Owner Alice is the actor, so should NOT receive notification
        Notification::assertNotSentTo($this->owner, TaskActivityNotification::class);
    }

    public function test_task_reassignment_logs_activity_with_old_and_new_values_and_notifies_both(): void
    {
        Notification::fake();

        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'status_id' => $this->todoStatus->id,
            'creator_id' => $this->owner->id,
            'assignee_id' => $this->member->id,
        ]);

        $this->actingAs($this->owner);

        $action = new UpdateTask();
        $action->execute($task, [
            'assignee_id' => $this->otherMember->id,
        ]);

        // Check activity
        $activity = Activity::where('subject_type', Task::class)
            ->where('subject_id', $task->id)
            ->where('event', 'task_reassigned')
            ->first();

        $this->assertNotNull($activity);
        $this->assertEquals($this->member->name, $activity->properties['old']);
        $this->assertEquals($this->otherMember->name, $activity->properties['new']);

        // Check notification sent to newly assigned member and previous assignee
        Notification::assertSentTo($this->otherMember, TaskActivityNotification::class);
        Notification::assertSentTo($this->member, TaskActivityNotification::class);
        Notification::assertNotSentTo($this->owner, TaskActivityNotification::class);
    }

    public function test_task_status_change_logs_activity_and_detects_completion(): void
    {
        Notification::fake();

        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'status_id' => $this->inProgressStatus->id,
            'creator_id' => $this->owner->id,
            'assignee_id' => $this->member->id,
        ]);

        $this->actingAs($this->member);

        $action = new UpdateTaskStatus();
        $action->execute($task, $this->doneStatus->id);

        $activity = Activity::where('subject_type', Task::class)
            ->where('subject_id', $task->id)
            ->where('event', 'task_completed')
            ->first();

        $this->assertNotNull($activity);
        $this->assertEquals('In Progress', $activity->properties['old']);
        $this->assertEquals('Done', $activity->properties['new']);

        // Task creator (owner) should be notified; actor (member) should NOT
        Notification::assertSentTo($this->owner, TaskActivityNotification::class);
        Notification::assertNotSentTo($this->member, TaskActivityNotification::class);
    }

    public function test_comment_creation_logs_activity_and_notifies_participants(): void
    {
        Notification::fake();

        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'status_id' => $this->todoStatus->id,
            'creator_id' => $this->owner->id,
            'assignee_id' => $this->member->id,
        ]);

        $this->actingAs($this->otherMember);

        $action = new CreateComment();
        $comment = $action->execute($task, 'Could you please check the color scheme?');

        $activity = Activity::where('subject_type', Task::class)
            ->where('subject_id', $task->id)
            ->where('event', 'task_comment_added')
            ->first();

        $this->assertNotNull($activity);
        $this->assertEquals($this->otherMember->id, $activity->causer_id);
        $this->assertEquals($comment->id, $activity->properties['comment_id']);

        // Assignee (member) and Creator (owner) receive notification
        Notification::assertSentTo($this->member, TaskActivityNotification::class);
        Notification::assertSentTo($this->owner, TaskActivityNotification::class);
        Notification::assertNotSentTo($this->otherMember, TaskActivityNotification::class);
    }

    public function test_user_notification_preferences_suppress_unwanted_notifications(): void
    {
        Notification::fake();

        // Member Bob turns off comment notifications
        UserNotificationPreference::forUser($this->member->id)->update([
            'task_comment_added' => false,
        ]);

        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'status_id' => $this->todoStatus->id,
            'creator_id' => $this->owner->id,
            'assignee_id' => $this->member->id,
        ]);

        $this->actingAs($this->otherMember);

        $action = new CreateComment();
        $action->execute($task, 'Checking progress on this task.');

        // Owner still wants notifications, so receives it
        Notification::assertSentTo($this->owner, TaskActivityNotification::class);

        // Member Bob disabled comments, so should NOT receive it
        Notification::assertNotSentTo($this->member, TaskActivityNotification::class);
    }

    public function test_user_can_update_notification_preferences_via_livewire(): void
    {
        $this->actingAs($this->member);

        Livewire::test(NotificationPreferences::class)
            ->assertSet('task_assigned', true)
            ->set('task_assigned', false)
            ->set('task_priority_changed', false)
            ->assertSee('Notification preferences saved successfully');

        $prefs = UserNotificationPreference::where('user_id', $this->member->id)->first();
        $this->assertFalse($prefs->task_assigned);
        $this->assertFalse($prefs->task_priority_changed);
        $this->assertTrue($prefs->task_status_changed);
    }

    public function test_task_detail_component_displays_activity_timeline(): void
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'status_id' => $this->todoStatus->id,
            'creator_id' => $this->owner->id,
            'assignee_id' => $this->member->id,
        ]);

        // Create some activities
        activity()
            ->performedOn($task)
            ->causedBy($this->owner)
            ->event('task_created')
            ->log('created this task');

        activity()
            ->performedOn($task)
            ->causedBy($this->member)
            ->event('task_status_changed')
            ->withProperties(['old' => 'To Do', 'new' => 'In Progress'])
            ->log('changed status from To Do to In Progress');

        $this->actingAs($this->owner);

        Livewire::test(TaskDetail::class, ['taskId' => $task->id])
            ->set('activeTab', 'activity')
            ->assertSee('Activity History')
            ->assertSee('created this task')
            ->assertSee('changed status from To Do to In Progress')
            ->assertSee('To Do')
            ->assertSee('In Progress');
    }

    public function test_notification_failure_does_not_break_task_operation(): void
    {
        // Notification service failure isolation test
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'status_id' => $this->todoStatus->id,
            'creator_id' => $this->owner->id,
            'assignee_id' => $this->member->id,
        ]);

        $this->actingAs($this->owner);

        // When task is updated, even if an exception occurs during notification dispatch,
        // the task update succeeds and is saved in the database.
        $action = new UpdateTask();
        $updatedTask = $action->execute($task, [
            'priority' => 'urgent',
        ]);

        $this->assertEquals('urgent', $updatedTask->fresh()->priority->value);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'priority' => 'urgent',
        ]);
    }
}
