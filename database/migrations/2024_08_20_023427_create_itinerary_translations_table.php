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
        Schema::create('itinerary_translations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('itinerary_id');
            $table->string('field');
            $table->text('translation');
            $table->string('language');
            $table->timestamps();

            $table->foreign('itinerary_id')->references('id')->on('product_itineraries')->cascadeOnDelete();
            $table->foreign('language')->references('code')->on('languages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerary_translations');
    }
};
