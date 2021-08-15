<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();

            $table->unsignedSmallInteger('model_id');
            $table->string('model_type');
            $table->bigInteger('reactId')->unsigned();
            $table->foreign('reactId')->references('id')->on('reacts')->onDelete('cascade');

            $table->bigInteger('senderId')->unsigned();
            $table->foreign('senderId')->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('likes');
    }
}
