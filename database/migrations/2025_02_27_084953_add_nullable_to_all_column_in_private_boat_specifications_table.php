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
        Schema::table('private_boat_specifications', function (Blueprint $table) {
            $table->string('model')->nullable()->change();
            $table->unsignedSmallInteger('year')->nullable()->change();
            $table->string('length')->nullable()->change();
            $table->unsignedInteger('cabins')->nullable()->change();
            $table->unsignedInteger('heads')->nullable()->change();
            $table->string('engine')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('private_boat_specifications', function (Blueprint $table) {
            //
        });
    }
};
