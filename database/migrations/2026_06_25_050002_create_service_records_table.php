<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('service_type');
            $table->date('date');
            $table->string('workshop')->nullable();
            $table->integer('odometer_km')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->enum('status', ['dijadual', 'dalam_proses', 'selesai'])->default('dijadual');
            $table->text('notes')->nullable();
            $table->json('items')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_records');
    }
};
