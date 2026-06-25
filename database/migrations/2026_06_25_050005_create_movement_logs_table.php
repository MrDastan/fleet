<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movement_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('department')->nullable();
            $table->string('purpose');
            $table->string('destination')->nullable();
            $table->dateTime('checkout_time')->nullable();
            $table->dateTime('checkin_time')->nullable();
            $table->integer('km_out')->nullable();
            $table->integer('km_in')->nullable();
            $table->text('guard_notes')->nullable();
            $table->enum('status', ['di_luar', 'kembali'])->default('di_luar');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movement_logs');
    }
};
