<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class AltynAsyrController extends Controller
{

    public function redirect(){
        // register
        $this->registerOrder();
    }

    private function registerOrder(){
        $altynAsyr = app('App\Payment\AltynAsyr');
        try{
            $result =  $altynAsyr->registerOrder();
//            dd(json_decode($result,true));
            //todo handle api result(redirect to payment gateway)

        }catch (\Exception $exception){
//            dd($exception);
            //todo handle connection exception
        }
    }

    public function success($cart_id){

    }
}
