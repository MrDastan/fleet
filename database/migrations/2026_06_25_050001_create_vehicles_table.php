<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plat')->unique();
            $table->string('model');
            $table->string('type')->nullable();
            $table->integer('year')->nullable();
            $table->string('color')->nullable();
            $table->string('engine_no')->nullable();
            $table->string('chassis_no')->nullable();
            $table->string('department')->nullable();
            $table->integer('odometer_km')->default(0);
            $table->enum('status', ['aktif', 'servis', 'rosak', 'tidak_aktif'])->default('aktif');
            $table->string('emoji', 10)->default('🚗');
            $table->date('roadtax_expiry')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->date('puspakom_expiry')->nullable();
            $table->date('next_service_date')->nullable();
            $table->integer('next_service_km')->nullable();
            $table->string('qr_code_token')->unique()->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
