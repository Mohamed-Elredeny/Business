<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMentionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mention', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('mentionTypeId')->unsigned();
            $table->foreign('mentionTypeId')->references('id')->on('mention_types')->onDelete('cascade');


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
        Schema::dropIfExists('mention');
    }
}
