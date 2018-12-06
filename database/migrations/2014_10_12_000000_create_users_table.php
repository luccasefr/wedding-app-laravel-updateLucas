<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_1');
            $table->string('name_2');
            $table->string('email')->unique();
            $table->string('password');
            $table->dateTime('wedding_date')->nullable();
            $table->double('want_to_spent')->nullable();
            $table->integer('waiting_guests');
            $table->integer('wedding_address_id')->nullable();
            $table->boolean('quiz_released')->default(false);
            $table->boolean('puzzle_released')->default(false);
            $table->boolean('memory_game_released')->default(false);
            $table->boolean('publications_should_be_aproved')->default(false);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
