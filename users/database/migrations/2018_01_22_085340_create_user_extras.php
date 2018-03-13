<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserExtras extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_extras', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_user')->unsigned();
            $table->foreign('id_user')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('dni')->nullable();
            $table->date('birthday')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone',20)->nullable();
            $table->string('telephone_alt',20)->nullable();
            $table->string('address')->nullable();
            $table->string('cp',10)->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('country')->nullable();
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
        Schema::dropIfExists('user_extras');
    }
}
