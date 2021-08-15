<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('body');
            $table->unsignedFloat('price','15','3')->nullable();
            //PostType
            $table->bigInteger('postTypeId')->unsigned();
            $table->foreign('postTypeId')->references('id')->on('posts_types')->onDelete('cascade');

            $table->bigInteger('privacyId')->unsigned();
            $table->foreign('privacyId')->references('id')->on('privacy_type')->onDelete('cascade');

            $table->bigInteger('stateId')->unsigned();
            $table->foreign('stateId')->references('id')->on('states')->onDelete('cascade');

            $table->bigInteger('publisherId')->unsigned();
            $table->foreign('publisherId')->references('id')->on('users')->onDelete('cascade');

            $table->bigInteger('categoryId')->unsigned();
            $table->foreign('categoryId')->references('id')->on('categories')->onDelete('cascade');

            $table->bigInteger('group_id')->unsigned()->nullable();
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');

            $table->bigInteger('page_id')->unsigned()->nullable();
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');

            $table->bigInteger('post_id')->unsigned()->nullable();
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');

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
        Schema::dropIfExists('posts');
    }
}
