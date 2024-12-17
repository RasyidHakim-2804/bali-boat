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
        Schema::table('private_boat_order_forms', function (Blueprint $table) {
            $table->dropForeign(['variant_id']);
            $table->foreign('variant_id')->references('id')->on('private_boat_variants');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('private_boat_order_forms', function (Blueprint $table) {
            //
        });
    }
};
