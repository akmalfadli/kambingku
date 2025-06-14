<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weight_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goat_id')->constrained();
            $table->decimal('weight', 8, 2);
            $table->date('weigh_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['goat_id', 'weigh_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weight_logs');
    }
};
