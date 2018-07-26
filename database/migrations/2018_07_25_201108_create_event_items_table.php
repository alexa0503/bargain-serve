<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_id')->unsigned();
            $table->foreign('event_id')->references('id')->on('events');
            $table->integer('item_id')->unsigned();
            $table->foreign('item_id')->references('id')->on('items');
            $table->integer('shop_id')->unsigned();
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->integer('total_num')->index();
            $table->integer('exchanged_num')->default(0)->index();
            $table->integer('winned_num')->default(0)->index();
            $table->integer('bargained_num')->default(0)->index();
            $table->decimal('origin_price',10,2);
            $table->decimal('bargain_price',10,2);
            $table->integer('bargain_min_times')->default('9');
            $table->integer('bargain_max_times')->default('29');
            $table->decimal('bargain_min_price',10,2);
            $table->decimal('bargain_max_price',10,2);
            $table->boolean('is_released');
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
        Schema::dropIfExists('event_items');
    }
}
