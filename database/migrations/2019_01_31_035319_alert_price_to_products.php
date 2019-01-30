<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlertPriceToProducts extends Migration
{
    /**
     * Run the migrations.
     * ALTER TABLE `n_products` ADD COLUMN `price`  int NOT NULL DEFAULT 0 COMMENT '产品价格 *10000 保留四位' AFTER `uid`;
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('price')->default(0)->comment('产品价格 *10000 保留四位')->after('uid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
