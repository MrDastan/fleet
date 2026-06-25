<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roadtax_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->enum('doc_type', ['roadtax', 'insuran', 'puspakom']);
            $table->date('start_date')->nullable();
            $table->date('expiry_date');
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('policy_no')->nullable();
            $table->string('status')->default('aktif');
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roadtax_records');
    }
};
