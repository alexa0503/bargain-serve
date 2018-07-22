<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('name');
            $table->text('header_images');
            $table->text('kv_images');
            $table->string('tel', 100);
            $table->text('descr');
            $table->text('share_title');
            $table->text('share_desc');
            $table->text('share_image');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('visit_times');
            $table->integer('max_item_num');
            $table->boolean('is_activated');
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
        Schema::dropIfExists('shops');
    }
}
