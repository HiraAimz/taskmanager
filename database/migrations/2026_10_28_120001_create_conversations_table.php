<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')
                ->constrained('workspaces')
                ->cascadeOnDelete();
            $table->foreignId('user_one_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('user_two_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->timestamp('last_message_at')->nullable()->index();
            $table->timestamps();

            $table->unique(['workspace_id', 'user_one_id', 'user_two_id'], 'conv_ws_user_pair_unique');
            $table->index(['user_one_id', 'workspace_id']);
            $table->index(['user_two_id', 'workspace_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
