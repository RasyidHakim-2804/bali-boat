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
        Schema::create('order_refunds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fast_boat_order_id');
            $table->foreign('fast_boat_order_id')->references('id')->on('fast_boat_orders')->cascadeOnDelete();
            $table->unsignedBigInteger('xendit_payout_link_id')->nullable();
            $table->foreign('xendit_payout_link_id')->references('id')->on('xendit_payout_links');
            $table->string('status');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_refunds');
    }
};
