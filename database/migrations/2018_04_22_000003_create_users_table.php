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
            $table->string('name', 100);
            $table->string('openid',120)->unique();
            $table->string('unionid',120)->nullable()->default(null)->unique();
            $table->string('nickname', 100);
            $table->string('avatar');
            $table->string('province', 100);
            $table->string('country', 100);
            $table->string('city', 100);
            $table->string('tel', 100);
            $table->string('gender', 60);
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
