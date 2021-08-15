<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSponsoredTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sponsored', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('timeId')->unsigned();
            $table->foreign('timeId')->references('id')->on('sponsored_time')->onDelete('cascade');

            $table->bigInteger('reachId')->unsigned();
            $table->foreign('reachId')->references('id')->on('sponsored_reach')->onDelete('cascade');

            $table->bigInteger('postId')->unsigned();
            $table->foreign('postId')->references('id')->on('posts')->onDelete('cascade');

            $table->bigInteger('stateId')->unsigned();
            $table->foreign('stateId')->references('id')->on('states')->onDelete('cascade');

            $table->bigInteger('age_id')->unsigned();
            $table->foreign('age_id')->references('id')->on('sponsored_ages')->onDelete('cascade');

            $table->bigInteger('country_id')->unsigned();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');

            $table->bigInteger('city_id')->unsigned();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');

            $table->string('gender');
            $table->unsignedFloat('price');

            //Total price of the adv will be generated
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
        Schema::dropIfExists('sponsored');
    }
}
