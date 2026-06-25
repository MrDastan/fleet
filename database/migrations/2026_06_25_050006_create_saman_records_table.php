<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saman_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('saman_no')->unique();
            $table->string('saman_type');
            $table->string('offense');
            $table->string('offense_detail')->nullable();
            $table->date('date');
            $table->time('time')->nullable();
            $table->string('location');
            $table->string('location_detail')->nullable();
            $table->decimal('amount', 10, 2);
            $table->date('due_date')->nullable();
            $table->enum('status', ['belum_bayar', 'dalam_rayuan', 'telah_bayar'])->default('belum_bayar');
            $table->string('responsibility')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('receipt_no')->nullable();
            $table->string('receipt_file')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saman_records');
    }
};
