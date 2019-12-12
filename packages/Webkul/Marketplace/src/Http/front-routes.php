<?php

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency']], function () {

    //Marketplace routes starts here
    Route::prefix('marketplace')->group(function () {
        Route::get('/', 'Webkul\Marketplace\Http\Controllers\Shop\MarketplaceController@index')->defaults('_config', [
            'view' => 'marketplace::shop.seller-central.index'
        ])->name('marketplace.seller_central.index');

        Route::get('seller/profile/{url}', 'Webkul\Marketplace\Http\Controllers\Shop\SellerController@show')->defaults('_config', [
            'view' => 'marketplace::shop.sellers.profile'
        ])->name('marketplace.seller.show');

        Route::post('seller/url', 'Webkul\Marketplace\Http\Controllers\Shop\SellerController@checkShopUrl')->name('marketplace.seller.url');

        Route::post('seller/{url}/contact', 'Webkul\Marketplace\Http\Controllers\Shop\SellerController@contact')->name('marketplace.seller.contact');


        //Seller Review routes
        Route::get('seller/{url}/reviews', 'Webkul\Marketplace\Http\Controllers\Shop\ReviewController@index')->defaults('_config', [
            'view' => 'marketplace::shop.sellers.reviews.index'
        ])->name('marketplace.reviews.index');


        //Seller Products routes
        Route::get('seller/{url}/products', 'Webkul\Marketplace\Http\Controllers\Shop\ProductController@index')->defaults('_config', [
            'view' => 'marketplace::shop.sellers.products.index'
        ])->name('marketplace.products.index');


        // Auth Routes
        Route::group(['middleware' => ['customer']], function () {

            //Seller Review routes
            Route::get('seller/{url}/reviews/create', 'Webkul\Marketplace\Http\Controllers\Shop\ReviewController@create')->defaults('_config', [
                'view' => 'marketplace::shop.sellers.reviews.create'
            ])->name('marketplace.reviews.create');

            Route::post('seller/{url}/reviews/create', 'Webkul\Marketplace\Http\Controllers\Shop\ReviewController@store')->defaults('_config', [
                'redirect' => 'marketplace.seller.show'
            ])->name('marketplace.reviews.store');


            //Marketplace seller account
            Route::prefix('account')->group(function () {
                Route::get('create', 'Webkul\Marketplace\Http\Controllers\Shop\Account\SellerController@create')->defaults('_config', [
                    'view' => 'marketplace::shop.sellers.account.profile.create'
                ])->name('marketplace.account.seller.create');

                Route::post('create', 'Webkul\Marketplace\Http\Controllers\Shop\Account\SellerController@store')->defaults('_config', [
                    'redirect' => 'marketplace.account.seller.create'
                ])->name('marketplace.account.seller.store');

                Route::get('edit', 'Webkul\Marketplace\Http\Controllers\Shop\Account\SellerController@edit')->defaults('_config', [
                    'view' => 'marketplace::shop.sellers.account.profile.edit'
                ])->name('marketplace.account.seller.edit');

                Route::put('edit/{id}', 'Webkul\Marketplace\Http\Controllers\Shop\Account\SellerController@update')->defaults('_config', [
                    'redirect' => 'marketplace.account.seller.edit'
                ])->name('marketplace.account.seller.update');


                //Dashboard route
                Route::get('dashboard', 'Webkul\Marketplace\Http\Controllers\Shop\Account\DashboardController@index')->defaults('_config', [
                    'view' => 'marketplace::shop.sellers.account.dashboard.index'
                ])->name('marketplace.account.dashboard.index');


                // Catalog Routes
                Route::prefix('catalog')->group(function () {

                    // Catalog Product Routes
                    Route::get('/products', 'Webkul\Marketplace\Http\Controllers\Shop\Account\ProductController@index')->defaults('_config', [
                        'view' => 'marketplace::shop.sellers.account.catalog.products.index'
                    ])->name('marketplace.account.products.index');


                    // Catalog Product Routes
                    Route::get('/products/search', 'Webkul\Marketplace\Http\Controllers\Shop\Account\AssignProductController@index')->defaults('_config', [
                        'view' => 'marketplace::shop.sellers.account.catalog.products.search'
                    ])->name('marketplace.account.products.search');

                    Route::get('/products/assign/{id?}', 'Webkul\Marketplace\Http\Controllers\Shop\Account\AssignProductController@create')->defaults('_config', [
                        'view' => 'marketplace::shop.sellers.account.catalog.products.assign'
                    ])->name('marketplace.account.products.assign');

                    Route::post('/products/assign/{id?}', 'Webkul\Marketplace\Http\Controllers\Shop\Account\AssignProductController@store')->defaults('_config', [
                        'redirect' => 'marketplace.account.products.index'
                    ])->name('marketplace.account.products.assign-store');

                    Route::get('/products/edit-assign/{id}', 'Webkul\Marketplace\Http\Controllers\Shop\Account\AssignProductController@edit')->defaults('_config', [
                        'view' => 'marketplace::shop.sellers.account.catalog.products.edit-assign'
                    ])->name('marketplace.account.products.edit-assign');

                    Route::put('/products/edit-assign/{id}', 'Webkul\Marketplace\Http\Controllers\Shop\Account\AssignProductController@update')->defaults('_config', [
                        'redirect' => 'marketplace.account.products.index'
                    ])->name('marketplace.account.products.update');


                    Route::get('/products/create', 'Webkul\Marketplace\Http\Controllers\Shop\Account\ProductController@create')->defaults('_config', [
                        'view' => 'marketplace::shop.sellers.account.catalog.products.create'
                    ])->name('marketplace.account.products.create');

                    Route::post('/products/create', 'Webkul\Marketplace\Http\Controllers\Shop\Account\ProductController@store')->defaults('_config', [
                        'redirect' => 'marketplace.account.products.edit'
                    ])->name('marketplace.account.products.store');

                    Route::get('/products/edit/{id}', 'Webkul\Marketplace\Http\Controllers\Shop\Account\ProductController@edit')->defaults('_config', [
                        'view' => 'marketplace::shop.sellers.account.catalog.products.edit'
                    ])->name('marketplace.account.products.edit');

                    Route::put('/products/edit/{id}', 'Webkul\Marketplace\Http\Controllers\Shop\Account\ProductController@update')->defaults('_config', [
                        'redirect' => 'marketplace.account.products.index'
                    ])->name('marketplace.account.products.update');


                    Route::get('/products/delete/{id}', 'Webkul\Marketplace\Http\Controllers\Shop\Account\ProductController@destroy')->name('marketplace.account.products.delete');

                    Route::post('products/massdelete', 'Webkul\Marketplace\Http\Controllers\Shop\Account\ProductController@massDestroy')->defaults('_config', [
                        'redirect' => 'marketplace.account.products.index'
                    ])->name('marketplace.account.products.massdelete');
                });


                //Sales routes
                Route::prefix('sales')->group(function () {
                    Route::get('orders', 'Webkul\Marketplace\Http\Controllers\Shop\Account\Sales\OrderController@index')->defaults('_config', [
                        'view' => 'marketplace::shop.sellers.account.sales.orders.index'
                    ])->name('marketplace.account.orders.index');

                    Route::get('orders/view/{id}', 'Webkul\Marketplace\Http\Controllers\Shop\Account\Sales\OrderController@view')->defaults('_config', [
                        'view' => 'marketplace::shop.sellers.account.sales.orders.view'
                    ])->name('marketplace.account.orders.view');

                    Route::get('/orders/cancel/{id}', 'Webkul\Marketplace\Http\Controllers\Shop\Account\Sales\OrderController@cancel')->name('marketplace.account.orders.cancel');


                    // Sales Invoices Routes
                    Route::get('invoices/create/{order_id}', 'Webkul\Marketplace\Http\Controllers\Shop\Account\Sales\InvoiceController@create')->defaults('_config', [
                        'view' => 'marketplace::shop.sellers.account.sales.invoices.create'
                    ])->name('marketplace.account.invoices.create');

                    Route::post('invoices/create/{id}', 'Webkul\Marketplace\Http\Controllers\Shop\Account\Sales\InvoiceController@store')->defaults('_config', [
                        'redirect' => 'marketplace.account.orders.view'
                    ])->name('marketplace.account.invoices.store');

                    //Prints invoice
                    Route::get('invoices/print/{id}', 'Webkul\Marketplace\Http\Controllers\Shop\Account\Sales\InvoiceController@print')
                        ->name('marketplace.account.invoices.print');


                    // Sales Shipments Routes
                    Route::get('shipments/create/{order_id}', 'Webkul\Marketplace\Http\Controllers\Shop\Account\Sales\ShipmentController@create')->defaults('_config', [
                        'view' => 'marketplace::shop.sellers.account.sales.shipments.create'
                    ])->name('marketplace.account.shipments.create');

                    Route::post('shipments/create/{id}', 'Webkul\Marketplace\Http\Controllers\Shop\Account\Sales\ShipmentController@store')->defaults('_config', [
                        'redirect' => 'marketplace.account.orders.view'
                    ])->name('marketplace.account.shipments.store');


                    // Sales Transactions Routes
                    Route::get('transactions', 'Webkul\Marketplace\Http\Controllers\Shop\Account\Sales\TransactionController@index')->defaults('_config', [
                        'view' => 'marketplace::shop.sellers.account.sales.transactions.index'
                    ])->name('marketplace.account.transactions.index');

                    Route::get('transactions/view/{id}', 'Webkul\Marketplace\Http\Controllers\Shop\Account\Sales\TransactionController@view')->defaults('_config', [
                        'view' => 'marketplace::shop.sellers.account.sales.transactions.view'
                    ])->name('marketplace.account.transactions.view');
                });


                //Seller review routes
                Route::get('reviews', 'Webkul\Marketplace\Http\Controllers\Shop\Account\ReviewController@index')->defaults('_config', [
                    'view' => 'marketplace::shop.sellers.account.reviews.index'
                ])->name('marketplace.account.reviews.index');
            });
        });
    });
    //Marketplace routes end here

    //Seller review routes
    Route::get('products/{id}/offers', 'Webkul\Marketplace\Http\Controllers\Shop\ProductController@offers')->defaults('_config', [
        'view' => 'marketplace::shop.products.offers'
    ])->name('marketplace.product.offers.index');
});