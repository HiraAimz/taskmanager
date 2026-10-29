<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Actions\Projects\CreateProject;
use App\Enums\Priority;
use App\Enums\StatusType;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Tag;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        // Create test users
        $manager = User::create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
        ]);
        $manager->assignRole('Manager');

        $member1 = User::create([
            'name' => 'Member One',
            'email' => 'member1@example.com',
            'password' => Hash::make('password'),
        ]);
        $member1->assignRole('Member');

        $member2 = User::create([
            'name' => 'Member Two',
            'email' => 'member2@example.com',
            'password' => Hash::make('password'),
        ]);
        $member2->assignRole('Member');

        // Create 2 workspaces
        $workspace1 = Workspace::create([
            'name' => 'Marketing Team',
            'description' => 'Marketing campaigns and content',
            'owner_id' => $manager->id,
        ]);
        $workspace1->users()->attach([$manager->id, $member1->id, $member2->id]);

        $workspace2 = Workspace::create([
            'name' => 'Product Development',
            'description' => 'Product features and improvements',
            'owner_id' => $manager->id,
        ]);
        $workspace2->users()->attach([$manager->id, $member1->id]);

        // Create tags
        $tag1 = Tag::create(['workspace_id' => $workspace1->id, 'name' => 'urgent', 'color' => '#EF4444']);
        $tag2 = Tag::create(['workspace_id' => $workspace1->id, 'name' => 'bug', 'color' => '#F59E0B']);
        $tag3 = Tag::create(['workspace_id' => $workspace1->id, 'name' => 'feature', 'color' => '#10B981']);

        // Create 3 projects in workspace 1
        $action = new CreateProject;

        $project1 = $action->handle([
            'workspace_id' => $workspace1->id,
            'name' => 'Website Redesign',
            'description' => 'Complete overhaul of company website',
            'color' => '#3B82F6',
            'icon' => 'globe',
        ], $manager);

        $project2 = $action->handle([
            'workspace_id' => $workspace1->id,
            'name' => 'Content Strategy Q1',
            'description' => 'Plan and execute Q1 content calendar',
            'color' => '#8B5CF6',
            'icon' => 'document',
        ], $manager);

        $project3 = $action->handle([
            'workspace_id' => $workspace2->id,
            'name' => 'Mobile App v2',
            'description' => 'Next generation mobile application',
            'color' => '#10B981',
            'icon' => 'mobile',
        ], $manager);

        // Create lists for project 1
        $list1 = TaskList::create(['project_id' => $project1->id, 'name' => 'Design', 'position' => 0]);
        $list2 = TaskList::create(['project_id' => $project1->id, 'name' => 'Development', 'position' => 1]);
        $list3 = TaskList::create(['project_id' => $project1->id, 'name' => 'Testing', 'position' => 2]);

        // Get statuses
        $todoStatus = $project1->statuses()->where('type', StatusType::Todo)->first();
        $inProgressStatus = $project1->statuses()->where('type', StatusType::InProgress)->first();
        $doneStatus = $project1->statuses()->where('type', StatusType::Done)->first();

        // Create 25+ tasks
        $tasks = [
            ['title' => 'Create wireframes for homepage', 'list' => $list1, 'status' => $todoStatus, 'priority' => Priority::High, 'creator' => $manager, 'assignees' => [$member1]],
            ['title' => 'Design navigation menu', 'list' => $list1, 'status' => $inProgressStatus, 'priority' => Priority::Normal, 'creator' => $manager, 'assignees' => [$member1]],
            ['title' => 'Create color palette', 'list' => $list1, 'status' => $doneStatus, 'priority' => Priority::Normal, 'creator' => $member1, 'assignees' => [$member1]],
            ['title' => 'Set up React project', 'list' => $list2, 'status' => $todoStatus, 'priority' => Priority::Urgent, 'creator' => $manager, 'assignees' => [$member2]],
            ['title' => 'Build header component', 'list' => $list2, 'status' => $inProgressStatus, 'priority' => Priority::High, 'creator' => $member2, 'assignees' => [$member2]],
            ['title' => 'Implement responsive design', 'list' => $list2, 'status' => $todoStatus, 'priority' => Priority::Normal, 'creator' => $manager, 'assignees' => [$member1, $member2]],
            ['title' => 'Write unit tests', 'list' => $list3, 'status' => $todoStatus, 'priority' => Priority::Low, 'creator' => $manager, 'assignees' => [$member2]],
            ['title' => 'Cross-browser testing', 'list' => $list3, 'status' => $todoStatus, 'priority' => Priority::Normal, 'creator' => $member1, 'assignees' => [$member1]],
        ];

        foreach ($tasks as $index => $taskData) {
            $task = Task::create([
                'project_id' => $project1->id,
                'task_list_id' => $taskData['list']->id,
                'status_id' => $taskData['status']->id,
                'title' => $taskData['title'],
                'description' => 'Detailed description for: '.$taskData['title'],
                'priority' => $taskData['priority'],
                'creator_id' => $taskData['creator']->id,
                'due_date' => now()->addDays(rand(1, 30)),
                'estimated_minutes' => rand(30, 480),
                'position' => $index,
                'completed_at' => $taskData['status']->type === StatusType::Done ? now() : null,
            ]);

            $task->assignees()->attach($taskData['assignees']);

            if ($index % 3 === 0) {
                $task->tags()->attach([$tag1->id, $tag3->id]);
            }

            // Add comments to some tasks
            if ($index % 2 === 0) {
                Comment::create([
                    'commentable_type' => Task::class,
                    'commentable_id' => $task->id,
                    'user_id' => $manager->id,
                    'body' => 'Looking good! Keep up the progress.',
                ]);
            }
        }

        // Add more tasks to projects 2 and 3
        for ($i = 0; $i < 10; $i++) {
            $project = rand(0, 1) ? $project2 : $project3;
            $lists = $project->taskLists;

            if ($lists->isEmpty()) {
                $lists = collect([
                    TaskList::create(['project_id' => $project->id, 'name' => 'Backlog', 'position' => 0]),
                    TaskList::create(['project_id' => $project->id, 'name' => 'In Progress', 'position' => 1]),
                    TaskList::create(['project_id' => $project->id, 'name' => 'Done', 'position' => 2]),
                ]);
            }

            $statuses = $project->statuses;
            $status = $statuses->random();

            Task::create([
                'project_id' => $project->id,
                'task_list_id' => $lists->random()->id,
                'status_id' => $status->id,
                'title' => 'Task #'.($i + 1).' for '.$project->name,
                'description' => 'Description for task #'.($i + 1),
                'priority' => Priority::cases()[array_rand(Priority::cases())],
                'creator_id' => [$manager, $member1, $member2][array_rand([$manager, $member1, $member2])]->id,
                'due_date' => rand(0, 1) ? now()->addDays(rand(1, 60)) : null,
                'position' => $i,
                'completed_at' => $status->type === StatusType::Done ? now() : null,
            ]);
        }
    }
}
