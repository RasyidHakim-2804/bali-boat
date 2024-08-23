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
        Schema::create('fast_boat_order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->foreignId('fast_boat_id')->constrained('fast_boats', 'id');
            $table->unsignedBigInteger('schedule_id');
            $table->foreign('schedule_id')->references('id')->on('fast_boat_schedules')->cascadeOnDelete();
            $table->unsignedBigInteger('schedule_variant_id')->nullable();
            $table->foreign('schedule_variant_id')->references('id')->on('fast_boat_schedule_variants')->cascadeOnDelete();
            $table->foreignId('departure_harbor_id')->constrained('harbors', 'id');
            $table->foreignId('arrival_harbor_id')->constrained('harbors', 'id');
            $table->time('departure_time');
            $table->time('arrival_time');
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
        Schema::dropIfExists('fast_boat_order_details');
    }
};
