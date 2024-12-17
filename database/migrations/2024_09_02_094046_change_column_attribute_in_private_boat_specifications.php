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
            $table->unsignedSmallInteger('year')->change();
            $table->unsignedInteger('cabins')->change();
            $table->unsignedInteger('heads')->change();
            $table->dropForeign('private_boat_specifications_language_foreign');
            $table->dropColumn('language');
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
