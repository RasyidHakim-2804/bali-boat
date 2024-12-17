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
        Schema::create('xendit_payout_link_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('xendit_payout_link_id')->nullable();
            $table->foreign('xendit_payout_link_id')->references('id')->on('xendit_payout_links')->cascadeOnDelete();
            $table->string('status');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('xendit_payout_link_logs');
    }
};
