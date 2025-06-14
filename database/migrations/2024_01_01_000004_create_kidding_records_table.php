<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kidding_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pregnancy_id')->constrained();
            $table->foreignId('mother_goat_id')->constrained('goats');
            $table->date('delivery_date');
            $table->integer('number_of_kids');
            $table->json('kids_details'); // [{gender: 'male', tag_id: 'K001', survival_status: 'alive'}, ...]
            $table->text('delivery_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kidding_records');
    }
};
