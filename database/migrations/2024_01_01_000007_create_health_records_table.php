<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goat_id')->constrained();
            $table->date('record_date');
            $table->string('diagnosis');
            $table->text('treatment');
            $table->string('medicine_given')->nullable();
            $table->decimal('cost', 10, 2)->default(0);
            $table->string('vet_name')->nullable();
            $table->date('next_checkup_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['goat_id', 'record_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_records');
    }
};
