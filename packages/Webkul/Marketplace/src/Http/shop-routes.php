<?php
Route::group(['middleware' => ['web', 'theme', 'locale', 'currency']], function () {
    Route::get('province/{code}/sellers', 'Webkul\Marketplace\Http\Controllers\Shop\SellerController@showSellers')->defaults('_config', [
        'view' => 'marketplace::shop.seller-central.province-sellers'
    ])->name('marketplace.province.sellers.show');
});