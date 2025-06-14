<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_type'); // feed, medicine, labor, equipment, etc.
            $table->decimal('amount', 10, 2);
            $table->text('description');
            $table->foreignId('goat_id')->nullable()->constrained();
            $table->date('expense_date');
            $table->string('category')->nullable();
            $table->timestamps();

            $table->index(['expense_type', 'expense_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
