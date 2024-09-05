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
        Schema::create('faq_translations', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('faq_id');
            $table->foreign('faq_id')->references('id')->on('faqs');

            $table->string('field');
            $table->text('translation');
            $table->string('language');
            $table->timestamps();

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
        Schema::dropIfExists('faq_translations');
    }
};
