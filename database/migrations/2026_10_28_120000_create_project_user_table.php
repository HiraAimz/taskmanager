<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_user', function (Blueprint $table) {
            $table->foreignId('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('role')->default('member');
            $table->timestamps();

            $table->primary(['project_id', 'user_id']);
        });

        // Safely backfill existing projects: assign project owners and existing workspace members as project members
        if (Schema::hasTable('projects') && Schema::hasTable('workspace_user')) {
            $projects = DB::table('projects')->select('id', 'workspace_id', 'owner_id')->get();
            $now = now();
            foreach ($projects as $project) {
                // Add project owner as admin
                DB::table('project_user')->updateOrInsert(
                    ['project_id' => $project->id, 'user_id' => $project->owner_id],
                    ['role' => 'admin', 'created_at' => $now, 'updated_at' => $now]
                );

                // Add all users in the workspace to the project as well so existing valid access isn't disrupted
                $workspaceUsers = DB::table('workspace_user')
                    ->where('workspace_id', $project->workspace_id)
                    ->pluck('user_id');

                foreach ($workspaceUsers as $userId) {
                    DB::table('project_user')->updateOrInsert(
                        ['project_id' => $project->id, 'user_id' => $userId],
                        ['role' => 'member', 'created_at' => $now, 'updated_at' => $now]
                    );
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('project_user');
    }
};
