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
        Schema::create('fast_boat_schedule_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fast_boat_schedule_id')->constrained('fast_boat_schedules', 'id');
            $table->string('passenger_type')->nullable();
            $table->string('age_type')->nullable();
            $table->unsignedDecimal('price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fast_boat_schedule_variants');
    }
};
