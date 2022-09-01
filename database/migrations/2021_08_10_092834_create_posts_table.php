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
            $table->string('category')->comment('Cigars | Pipe Tobacco ');
            $table->string('image')->nullable();
            $table->bigInteger('flavour_category_id')->unsigned(); 
            $table->foreign('flavour_category_id')->references('id')->on('flavour_categories');
            $table->bigInteger('flavour_id')->nullable()->unsigned(); 
            $table->foreign('flavour_id')->references('id')->on('flavours');
            $table->text('comment')->nullable();
            $table->double('rate',5,2)->default(0);
            $table->bigInteger('post_by')->unsigned(); 
            $table->foreign('post_by')->references('id')->on('users');
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
        Schema::dropIfExists('posts');
    }
}
