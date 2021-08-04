<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationShelfToStockEntryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_entries', function (Blueprint $table) {
                 $table->unsignedBigInteger('location_shelf_id');
                 $table->foreign('location_shelf_id')->references('id')->on('location_shelves');
                 $table->string('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_entries', function (Blueprint $table) {
            //
        });
    }
}
