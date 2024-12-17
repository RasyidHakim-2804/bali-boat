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
        Schema::create('refund_request_pins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fast_boat_order_id')->nullable();
            $table->foreign('fast_boat_order_id')->references('id')->on('fast_boat_orders')->onDelete('cascade');
            $table->unsignedBigInteger('private_boat_order_id')->nullable();
            $table->foreign('private_boat_order_id')->references('id')->on('private_boat_orders')->onDelete('cascade');
            $table->string('pin');
            $table->timestamp('expired_at');
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
        Schema::dropIfExists('refund_request_pins');
    }
};
