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
        Schema::create('private_boats', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->unsignedBigInteger('pickup_harbor_id');
            $table->boolean('is_rented');
            $table->boolean('is_promo')->default(0);
            $table->unsignedBigInteger('order_count')->default(0);
            $table->boolean('active')->default(0);
            $table->timestamps();
            
            $table->foreign('pickup_harbor_id')->references('id')->on('harbors')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
