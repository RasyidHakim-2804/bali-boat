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
            $table->string('departure_harbor_name')->after('arrival_harbor_id');
            $table->string('arrival_harbor_name')->after('departure_harbor_name');
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
