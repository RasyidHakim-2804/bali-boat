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
        Schema::create('fast_boat_schedules', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(1);
            $table->foreignId('fast_boat_id')->constrained('fast_boats', 'id');
            $table->foreignId('departure_harbor_id')->constrained('harbors', 'id');
            $table->foreignId('arrival_harbor_id')->constrained('harbors', 'id');
            $table->time('departure_time');
            $table->time('arrival_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fast_boat_schedules');
    }
};
