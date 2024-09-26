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
        Schema::create('activity_order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->foreignId('product_id')->constrained('activities', 'id');
            $table->unsignedBigInteger('variant_id');
            $table->foreign('variant_id')->references('id')->on('activity_variants')->cascadeOnDelete();
            $table->foreignId('location_id')->constrained('locations', 'id');
            $table->string('location_name');
            $table->string('quantity');
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->string('image')->nullable();
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
        Schema::dropIfExists('activity_order_details');
    }
};
