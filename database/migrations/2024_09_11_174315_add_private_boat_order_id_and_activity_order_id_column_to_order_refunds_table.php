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
        Schema::table('order_refunds', function (Blueprint $table) {
            $table->unsignedBigInteger('private_boat_order_id')->nullable()->after('fast_boat_order_id');
            $table->foreign('private_boat_order_id')->references('id')->on('private_boat_orders')->onDelete('cascade');
            $table->unsignedBigInteger('activity_order_id')->nullable()->after('private_boat_order_id');
            $table->foreign('activity_order_id')->references('id')->on('activity_orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_refunds', function (Blueprint $table) {
            //
        });
    }
};
