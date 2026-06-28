<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->dateTime('due_at');
            $table->string('status')->default('pending');
            $table->string('priority')->default('medium');
            $table->string('demo_url')->nullable();
            $table->boolean('video_sent')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index('due_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demos');
    }
};
