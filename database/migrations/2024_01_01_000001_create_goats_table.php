<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goats', function (Blueprint $table) {
            $table->id();
            $table->string('tag_number')->unique();
            $table->string('name')->nullable();
            $table->string('breed');
            $table->enum('gender', ['male', 'female']);
            $table->date('date_of_birth');
            $table->enum('status', ['active', 'sold', 'dead'])->default('active');
            $table->enum('type', ['fattening', 'breeding']);
            $table->enum('origin', ['bought', 'born']);
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->decimal('current_weight', 8, 2)->nullable();
            $table->unsignedBigInteger('father_id')->nullable();
            $table->unsignedBigInteger('mother_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('father_id')->references('id')->on('goats')->onDelete('set null');
            $table->foreign('mother_id')->references('id')->on('goats')->onDelete('set null');
            $table->index(['status', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goats');
    }
};
