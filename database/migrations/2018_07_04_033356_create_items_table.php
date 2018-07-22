<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shop_id')->unsigned();
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->string('name');
            $table->string('image');
            $table->string('descr');
            $table->integer('total_num')->index();
            $table->integer('winned_num')->default(0)->index();
            $table->integer('bargained_num')->default(0)->index();
            $table->decimal('origin_price',10,2);
            $table->decimal('bargain_price',10,2);
            $table->boolean('is_posted'); //是否发布
            $table->string('exchange_password');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
