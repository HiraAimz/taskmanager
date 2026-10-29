<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->boolean('task_assigned')->default(true);
            $table->boolean('task_status_changed')->default(true);
            $table->boolean('task_comment_added')->default(true);
            $table->boolean('task_due_date_changed')->default(true);
            $table->boolean('task_priority_changed')->default(true);
            $table->boolean('task_updated')->default(true);
            $table->timestamps();
        });

        // Add index on activity_log for timeline queries if not already present
        if (Schema::hasTable('activity_log')) {
            Schema::table('activity_log', function (Blueprint $table) {
                $table->index(['subject_type', 'subject_id', 'created_at'], 'activity_log_subject_created_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('activity_log')) {
            Schema::table('activity_log', function (Blueprint $table) {
                $table->dropIndex('activity_log_subject_created_idx');
            });
        }

        Schema::dropIfExists('user_notification_preferences');
    }
};
