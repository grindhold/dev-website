<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTagPivot extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('posts_tag', function (Blueprint $table) {
      $table->integer('post_id');
      $table->integer('tag_id');

      $table->foreign('post_id')->references('id')->on('posts');
      $table->foreign('tag_id')->references('id')->on('tags');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::drop('posts_tag');
  }
}
