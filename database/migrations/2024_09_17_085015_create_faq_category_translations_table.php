<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faq_category_translations', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('faq_category_id');
            $table->string('field');
            $table->text('translation');
            $table->string('language');
            $table->timestamps();

            $table->foreign('faq_category_id')->references('id')->on('faq_categories');
            $table->foreign('language')->references('code')->on('languages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('faq_category_translations');
    }
};
