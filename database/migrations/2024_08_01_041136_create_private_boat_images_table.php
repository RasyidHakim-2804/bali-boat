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
        Schema::create('private_boat_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('private_boat_id')->nullable();
            $table->foreign('private_boat_id')->references('id')->on('private_boats')->cascadeOnDelete();
            $table->unsignedInteger('position')->nullable();
            $table->string('image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_images');
    }
};
