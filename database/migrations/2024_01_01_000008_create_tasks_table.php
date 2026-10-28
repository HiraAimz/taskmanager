<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();
            $table->foreignId('task_list_id')
                ->nullable()
                ->constrained('task_lists')
                ->cascadeOnDelete();
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('tasks')
                ->cascadeOnDelete();
            $table->foreignId('status_id')
                ->constrained('statuses')
                ->restrictOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('priority', ['urgent', 'high', 'normal', 'low'])->default('normal');
            $table->foreignId('creator_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->foreignId('assignee_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->integer('estimated_minutes')->nullable();
            $table->integer('position')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'status_id']);
            $table->index(['task_list_id', 'position']);
            $table->index('parent_id');
            $table->index('creator_id');
            $table->index('assignee_id');
            $table->index('due_date');
            $table->index('completed_at');
            $table->index('priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
