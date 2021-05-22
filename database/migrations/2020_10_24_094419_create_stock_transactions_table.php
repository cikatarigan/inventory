<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('start_balance');
            $table->unsignedInteger('amount');
            $table->unsignedInteger('end_balance');
            $table->string('type');
            $table->string('detailable_type');
            $table->string('detailable_id');
            $table->unsignedBigInteger('good_id');
            $table->unsignedBigInteger('user_id'); 
            $table->unsignedBigInteger('location_id'); 
            $table->timestamps();
            $table->foreign('good_id')->references('id')->on('goods');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('location_id')->references('id')->on('locations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_transactions');
    }
}
