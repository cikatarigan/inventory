<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationShelfIdToAllotmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('allotments', function (Blueprint $table) {
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
        Schema::table('allotments', function (Blueprint $table) {
            //
        });
    }
}
