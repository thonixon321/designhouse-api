<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDesignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('designs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned(); //unsigned means it references another table
            $table->string('image');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('slug')->nullable();
            $table->boolean('is_live')->default(false);
            $table->timestamps();
            //if we don't do the cascade on delete here, this reference will prevent a user with designs from being deleted because of the reference to child designs. We will be doing the cascade on delete in a different spot for images as well
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('designs');
    }
}
