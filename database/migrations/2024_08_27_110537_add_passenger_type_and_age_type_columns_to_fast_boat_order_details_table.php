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
        Schema::table('fast_boat_order_details', function (Blueprint $table) {
            $table->string('passenger_type')->after('name');
            $table->string('age_type')->after('passenger_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fast_boat_order_details', function (Blueprint $table) {
            //
        });
    }
};
