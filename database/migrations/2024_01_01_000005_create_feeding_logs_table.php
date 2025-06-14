<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feeding_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goat_id')->nullable()->constrained();
            $table->string('feed_type'); // grass, concentrate, mix
            $table->decimal('quantity', 8, 2);
            $table->string('unit')->default('kg');
            $table->decimal('cost', 10, 2);
            $table->date('feeding_date');
            $table->boolean('is_group_feeding')->default(false);
            $table->json('goat_ids')->nullable(); // for group feeding
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['feeding_date', 'goat_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feeding_logs');
    }
};

