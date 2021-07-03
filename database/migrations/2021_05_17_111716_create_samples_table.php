<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSamplesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('samples', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('cupboard');
            $table->string('code');
            $table->string('years');
            $table->string('batch')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('planting_year')->nullable();
            $table->string('division')->nullable();
            $table->string('block')->nullable();
            $table->string('row')->nullable();
            $table->string('number_tree');
            $table->text('location');
            $table->string('photo');
            $table->boolean('is_deleted')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('samples');
    }
}
