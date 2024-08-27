<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pivot_private_boat_includes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('private_boat_id');
            $table->unsignedBigInteger('include_id');
            $table->unsignedInteger('position');
            $table->timestamps();

            $table->foreign('private_boat_id')->references('id')->on('private_boats');
            $table->foreign('include_id')->references('id')->on('product_includes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pivot_private_boat_includes');
    }
};
