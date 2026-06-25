<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('datetime');
            $table->string('station')->nullable();
            $table->enum('fuel_type', ['RON95', 'RON97', 'Diesel'])->default('RON95');
            $table->decimal('liters', 8, 2);
            $table->decimal('price_per_liter', 6, 2)->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->integer('odometer_km');
            $table->decimal('consumption_l100km', 5, 1)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_records');
    }
};
