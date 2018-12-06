<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->string('id');
            $table->primary('id');
            $table->integer('user_id');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->integer('age')->nullable();
            $table->boolean('confirmed')->default(false);
            $table->boolean('is_on_singles_meeting')->default(false);
            $table->string('profile_img')->nullable();
            $table->string('photo1_url')->nullable();
            $table->string('photo2_url')->nullable();
            $table->string('photo3_url')->nullable();
            $table->boolean('is_user')->default(false);
            $table->string('about')->nullable();
            $table->integer('gender_id')->nullable();
            $table->integer('want_gender_id')->nullable();
            $table->string('fcm_device_token')->nullable();
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
        Schema::dropIfExists('guests');
    }
}
