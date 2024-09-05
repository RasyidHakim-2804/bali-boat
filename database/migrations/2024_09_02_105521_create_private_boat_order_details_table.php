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
        Schema::create('private_boat_order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->foreignId('product_id')->constrained('private_boat_orders', 'id');
            $table->unsignedBigInteger('variant_id');
            $table->foreign('variant_id')->references('id')->on('private_boat_variants')->cascadeOnDelete();
            $table->foreignId('pickup_harbor_id')->constrained('harbors', 'id');
            $table->string('quantity');
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->string('image')->nullable();
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('private_boat_order_details');
    }
};
