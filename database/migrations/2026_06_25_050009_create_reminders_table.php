<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reminder_type');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date');
            $table->integer('days_remaining')->default(0);
            $table->enum('severity', ['critical', 'warning', 'info'])->default('info');
            $table->boolean('is_read')->default(false);
            $table->json('notified_roles')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
