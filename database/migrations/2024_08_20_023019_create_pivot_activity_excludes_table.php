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
        Schema::create('pivot_activity_excludes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_id');
            $table->unsignedBigInteger('exclude_id');
            $table->unsignedInteger('position');
            $table->timestamps();

            $table->foreign('activity_id')->references('id')->on('activities');
            $table->foreign('exclude_id')->references('id')->on('product_excludes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pivot_activity_excludes');
    }
};
