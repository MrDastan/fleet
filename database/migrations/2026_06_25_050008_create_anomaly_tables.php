<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anomaly_rules', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('severity', ['critical', 'warning', 'info'])->default('warning');
            $table->string('condition_type');
            $table->json('threshold')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('check_interval_minutes')->default(15);
            $table->timestamps();
        });

        Schema::create('anomaly_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('rule_code');
            $table->enum('severity', ['critical', 'warning', 'info'])->default('warning');
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('detected_data')->nullable();
            $table->enum('status', ['open', 'investigating', 'resolved'])->default('open');
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anomaly_records');
        Schema::dropIfExists('anomaly_rules');
    }
};
