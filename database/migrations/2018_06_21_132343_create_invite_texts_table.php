<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInviteTextsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invite_texts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('text');
            $table->double('width');
            $table->double('height');
            $table->double('x');
            $table->double('y');
            $table->integer('invite_id');
            $table->string('hexColor')->default('#000000');
            $table->integer('layer');
            $table->integer('font_id');
            $table->double('font_size');
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
        Schema::dropIfExists('invite_texts');
    }
}
