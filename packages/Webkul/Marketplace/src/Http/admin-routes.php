<?php

Route::group(['middleware' => ['web']], function () {

    Route::prefix('admin/marketplace')->group(function () {

        Route::group(['middleware' => ['admin']], function () {

            //Seller routes
            Route::get('sellers', 'Webkul\Marketplace\Http\Controllers\Admin\SellerController@index')->defaults('_config', [
                'view' => 'marketplace::admin.sellers.index'
            ])->name('admin.marketplace.sellers.index');

            Route::get('sellers/delete/{id}', 'Webkul\Marketplace\Http\Controllers\Admin\SellerController@destroy')
                ->name('admin.marketplace.sellers.delete');

            Route::post('sellers/massdelete', 'Webkul\Marketplace\Http\Controllers\Admin\SellerController@massDestroy')->defaults('_config', [
                'redirect' => 'admin.marketplace.sellers.index'
            ])->name('admin.marketplace.sellers.massdelete');

            Route::post('sellers/massupdate', 'Webkul\Marketplace\Http\Controllers\Admin\SellerController@massUpdate')->defaults('_config', [
                'redirect' => 'admin.marketplace.sellers.index'
            ])->name('admin.marketplace.sellers.massupdate');

            Route::get('sellers/{id}/orders', 'Webkul\Marketplace\Http\Controllers\Admin\OrderController@index')->defaults('_config', [
                'view' => 'marketplace::admin.orders.index'
            ])->name('admin.marketplace.sellers.orders.index');

            Route::get('orders', 'Webkul\Marketplace\Http\Controllers\Admin\OrderController@index')->defaults('_config', [
                'view' => 'marketplace::admin.orders.index'
            ])->name('admin.marketplace.orders.index');

            Route::post('orders', 'Webkul\Marketplace\Http\Controllers\Admin\OrderController@pay')->defaults('_config', [
                'redirect' => 'admin.marketplace.orders.index'
            ])->name('admin.marketplace.orders.pay');

            Route::get('transactions', 'Webkul\Marketplace\Http\Controllers\Admin\TransactionController@index')->defaults('_config', [
                'view' => 'marketplace::admin.transactions.index'
            ])->name('admin.marketplace.transactions.index');


            //Seller products routes
            Route::get('products', 'Webkul\Marketplace\Http\Controllers\Admin\ProductController@index')->defaults('_config', [
                'view' => 'marketplace::admin.products.index'
            ])->name('admin.marketplace.products.index');

            Route::get('products/delete/{id}', 'Webkul\Marketplace\Http\Controllers\Admin\ProductController@destroy')
                ->name('admin.marketplace.products.delete');

            Route::post('products/massdelete', 'Webkul\Marketplace\Http\Controllers\Admin\ProductController@massDestroy')->defaults('_config', [
                'redirect' => 'admin.marketplace.products.index'
            ])->name('admin.marketplace.products.massdelete');

            Route::post('products/massupdate', 'Webkul\Marketplace\Http\Controllers\Admin\ProductController@massUpdate')->defaults('_config', [
                'redirect' => 'admin.marketplace.products.index'
            ])->name('admin.marketplace.products.massupdate');


            //Seller review routes
            Route::get('reviews', 'Webkul\Marketplace\Http\Controllers\Admin\ReviewController@index')->defaults('_config', [
                'view' => 'marketplace::admin.reviews.index'
            ])->name('admin.marketplace.reviews.index');

            Route::post('reviews/massupdate', 'Webkul\Marketplace\Http\Controllers\Admin\ReviewController@massUpdate')->defaults('_config', [
                'redirect' => 'admin.marketplace.reviews.index'
            ])->name('admin.marketplace.reviews.massupdate');

        });

    });

});