<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('business_name')->nullable();
            $table->string('sector')->nullable();
            $table->string('city')->nullable();
            $table->string('source')->default('instagram');
            $table->string('contact_url')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('status')->default('contacted');
            $table->string('potential')->default('medium');
            $table->date('contacted_at')->nullable();
            $table->dateTime('next_follow_up_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'potential']);
            $table->index('contacted_at');
            $table->index('next_follow_up_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
