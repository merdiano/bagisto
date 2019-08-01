<?php
/**
 * Created by PhpStorm.
 * User: merdan
 * Date: 7/26/2019
 * Time: 16:49
 */
Route::group(['middleware' => ['web']], function () {
    Route::prefix('card/payment')->group(function () {
        Route::get('/redirect', 'Payment\Http\Controllers\AltynAsyrController@redirect')->name('paymentmethod.altynasyr.redirect');
        Route::get('/success', 'Payment\Http\Controllers\AltynAsyrController@success')
            ->name('paymentmethod.altynasyr.success')
            ->middleware('theme');
        Route::get('/cancel', 'Payment\Http\Controllers\AltynAsyrController@cancel')->name('paymentmethod.altynasyr.cancel');

    });
});