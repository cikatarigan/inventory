<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationShelfIdToGiveBacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('give_backs', function (Blueprint $table) {
            $table->unsignedBigInteger('location_shelf_id');
            $table->unsignedBigInteger('borrow_id');
            $table->foreign('borrow_id')->references('id')->on('borrows');
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
        Schema::table('give_backs', function (Blueprint $table) {
            //
        });
    }
}
