<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketplaceProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marketplace_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('condition')->nullable();
            $table->double('price')->default(0);
            $table->text('description')->nullable();

            $table->boolean('is_approved')->nullable();
            $table->boolean('is_owner')->default(0);
            $table->integer('parent_id')->unsigned()->nullable();

            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->integer('marketplace_seller_id')->unsigned();
            $table->foreign('marketplace_seller_id')->references('id')->on('marketplace_sellers')->onDelete('cascade');
            $table->unique(['marketplace_seller_id', 'product_id']);
            
            $table->timestamps();
        });

        Schema::table('marketplace_products', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('marketplace_products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('marketplace_products');
    }
}