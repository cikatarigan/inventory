<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('good_id');
            $table->unsignedInteger('amount');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('location_shelf_id');
            $table->string('barcode');
            $table->foreign('good_id')->references('id')->on('goods');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('location_shelf_id')->references('id')->on('location_shelves');
            $table->date('date_expired')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_entries');
    }
}
