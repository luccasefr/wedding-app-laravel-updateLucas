<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->boolean('expense')->default(false);
            $table->double('expense_value')->nullable();
            $table->date('expense_date')->nullable();
            $table->boolean('notify_guests')->default(false);
            $table->date('notify_date_from')->nullable();
            $table->date('notify_date_to')->nullable();
            $table->string('message')->nullable();
            $table->integer('user_id');
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
        Schema::dropIfExists('actions');
    }
}
