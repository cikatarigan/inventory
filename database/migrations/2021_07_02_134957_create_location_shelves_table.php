<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationShelvesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location_shelves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name_shelf');
            $table->foreign('location_id')->references('id')->on('locations');
            $table->foreign('user_id')->references('id')->on('users');
            $table->softDeletes();
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
        Schema::dropIfExists('location_shelves');
    }
}
