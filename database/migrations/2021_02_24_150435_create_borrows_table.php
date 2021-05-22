<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBorrowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('borrows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('good_id');
            $table->unsignedInteger('amount');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('handle_by');
            $table->foreign('good_id')->references('id')->on('goods');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('handle_by')->references('id')->on('users');
            $table->text('description');
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
        Schema::dropIfExists('borrows');
    }
}
