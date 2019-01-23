<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uid')->comment('用户id');
            $table->integer('cid')->comment('客户id');
            $table->integer('pid')->comment('关联产品名id');
            $table->integer('price')->comment('单价价格');
            $table->integer('requirement')->comment('需求量');
            $table->timestamp('time')->comment('下单时间');
            $table->unique(['uid','cid','pid','time']);
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
        Schema::dropIfExists('orders');
    }
}
