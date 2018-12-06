<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInviteImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invite_images', function (Blueprint $table) {
            $table->increments('id');
            $table->string('image_url');
            $table->double('width');
            $table->double('height');
            $table->double('x');
            $table->double('y');
            $table->integer('layer');
            $table->integer('invite_id');
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
        Schema::dropIfExists('invite_images');
    }
}
