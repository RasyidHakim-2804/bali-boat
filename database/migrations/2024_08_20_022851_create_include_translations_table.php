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
        Schema::create('include_translations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('include_id');
            $table->string('field');
            $table->text('translation');
            $table->string('language');
            $table->timestamps();

            $table->foreign('include_id')->references('id')->on('product_includes')->cascadeOnDelete();
            $table->foreign('language')->references('code')->on('languages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('include_translations');
    }
};
