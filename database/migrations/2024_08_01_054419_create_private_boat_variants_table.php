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
        Schema::create('private_boat_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('private_boat_id');
            $table->foreign('private_boat_id')->references('id')->on('private_boats')->cascadeOnDelete();
            $table->string('passenger_type')->nullable();
            $table->string('age_type')->nullable();
            $table->boolean('is_default')->default(0);
            $table->unsignedDecimal('price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_variants');
    }
};
