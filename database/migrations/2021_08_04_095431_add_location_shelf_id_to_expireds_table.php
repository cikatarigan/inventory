<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationShelfIdToExpiredsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expireds', function (Blueprint $table) {
            $table->unsignedBigInteger('location_shelf_id');
            $table->foreign('location_shelf_id')->references('id')->on('location_shelves');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expireds', function (Blueprint $table) {
            //
        });
    }
}
