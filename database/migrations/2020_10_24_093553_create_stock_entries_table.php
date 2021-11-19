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
            $table->unsignedInteger('stock_use');
            $table->unsignedBigInteger('user_id');
            $table->string('qrcode');
            $table->foreign('good_id')->references('id')->on('goods');
            $table->foreign('user_id')->references('id')->on('users');
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
