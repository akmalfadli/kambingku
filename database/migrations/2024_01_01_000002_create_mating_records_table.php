<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mating_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('male_goat_id')->constrained('goats');
            $table->foreignId('female_goat_id')->constrained('goats');
            $table->date('mating_date');
            $table->date('expected_delivery_date');
            $table->enum('outcome', ['pending', 'successful', 'failed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['mating_date', 'expected_delivery_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mating_records');
    }
};
