<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fast_boats', function (Blueprint $table) {
            $table->boolean('is_promo')->default(0)->after('is_rented');
            $table->unsignedBigInteger('order_count')->default(0)->after('is_promo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fast_boats', function (Blueprint $table) {
            //
        });
    }
};
