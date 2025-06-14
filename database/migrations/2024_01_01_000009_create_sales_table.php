<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goat_id')->constrained();
            $table->date('sale_date');
            $table->decimal('sale_price', 10, 2);
            $table->string('buyer_name')->nullable();
            $table->string('buyer_contact')->nullable();
            $table->decimal('total_expenses', 10, 2)->default(0);
            $table->decimal('profit', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['sale_date', 'goat_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
