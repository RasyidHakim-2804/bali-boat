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
        Schema::create('pivot_activity_includes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('include_id');
            $table->unsignedBigInteger('activity_id');
            $table->unsignedInteger('position');
            $table->timestamps();

            $table->foreign('include_id')->references('id')->on('product_includes');
            $table->foreign('activity_id')->references('id')->on('activities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pivot_activity_includes');
    }
};
