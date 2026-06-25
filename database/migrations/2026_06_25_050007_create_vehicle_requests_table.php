<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('request_no')->unique();
            $table->date('use_date');
            $table->time('time_start');
            $table->time('time_end');
            $table->string('purpose');
            $table->string('destination');
            $table->string('passengers')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending_guard', 'pending_fleet', 'approved', 'rejected', 'completed'])->default('pending_guard');
            $table->tinyInteger('stage')->default(1);
            $table->foreignId('guard_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('guard_note')->nullable();
            $table->json('guard_checklist')->nullable();
            $table->integer('guard_odometer')->nullable();
            $table->dateTime('guard_action_at')->nullable();
            $table->foreignId('fleet_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('fleet_note')->nullable();
            $table->string('fleet_priority')->nullable();
            $table->dateTime('fleet_action_at')->nullable();
            $table->foreignId('admin_override_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_override_reason')->nullable();
            $table->dateTime('admin_override_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_requests');
    }
};
