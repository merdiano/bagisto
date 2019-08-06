<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderIdToCartPaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart_payment', function (Blueprint $table) {
            $table->string('order_id')->nullable();
//            $table->unsignedSmallInteger('OrderStatus')->nullable();
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cart_payment', function (Blueprint $table) {
            $table->dropColumn('order_id');
//            $table->dropColumn('OrderStatus');
        });
    }
}
