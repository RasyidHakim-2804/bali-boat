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
        Schema::create('private_boat_specifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('private_boat_id');
            $table->foreign('private_boat_id')->references('id')->on('private_boats')->cascadeOnDelete();
            $table->string('model');
            $table->string('year');
            $table->string('length');
            $table->string('cabins');
            $table->string('heads');
            $table->string('engine');
            $table->string('language');
            $table->timestamps();

            $table->foreign('language')->references('code')->on('languages');
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_specifications');
    }
};
