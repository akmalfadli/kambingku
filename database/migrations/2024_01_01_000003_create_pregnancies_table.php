<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pregnancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('female_goat_id')->constrained('goats');
            $table->foreignId('mating_record_id')->constrained();
            $table->date('start_date');
            $table->date('expected_delivery_date');
            $table->date('actual_delivery_date')->nullable();
            $table->enum('status', ['pregnant', 'delivered', 'failed'])->default('pregnant');
            $table->text('health_notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'expected_delivery_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pregnancies');
    }
};
