<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('goats', function (Blueprint $table) {
            $table->decimal('buy_price', 15, 2)->nullable()->after('notes');
            $table->date('buy_date')->nullable()->after('buy_price');
            $table->string('supplier_name')->nullable()->after('buy_date');
        });
    }

    public function down()
    {
        Schema::table('goats', function (Blueprint $table) {
            $table->dropColumn(['buy_price', 'buy_date', 'supplier_name']);
        });
    }
};
