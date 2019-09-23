<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('slug');
            $table->text('title');
            $table->text('author');
            $table->text('city')->nullable();
            $table->text('year')->nullable();
            $table->text('pdf')->nullable();
            $table->unsignedBigInteger('subpage_id');
            $table->timestamps();

            $table->foreign('subpage_id')->references('id')->on('subpages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_posts');
    }
}
