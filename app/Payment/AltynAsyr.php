<?php
/**
 * Created by PhpStorm.
 * User: merdan
 * Date: 7/24/2019
 * Time: 16:48
 */

namespace App\Payment;


use GuzzleHttp\Client;
use Webkul\Payment\Payment\Payment;

class AltynAsyr extends Payment
{
    protected $code  = 'altynasyr';

    public function getRedirectUrl()
    {
        return route('card.altynasyr.redirect');
    }

    private function getApiClient():Client{
        return new Client([
            'base_uri' => $this->getConfigData('api_url'),
            'connect_timeout' => 15,
            'timeout' => 15,
            'verify' => true,
        ]);
    }

    public function registerOrder(){
        $client = $this->getApiClient();
        $cart = $this->getCart();
        $options =[
            'form_params' => [
                'userName' => '103161020074',
                'password' => 'E12wKp7a7vD8',
                'orderNumber' => $cart->id,
                'currency' => 934,
                'language' => 'ru',
                'description'=> 'bagisto order',
                'amount' =>$cart->grand_total * 100,// amount w kopeykah
                'returnUrl' => route('card.altynasyr.success'),
                //'failUrl' => route('paymentFail', $order->id)
            ],
//            'business' => $this->getConfigData('business_account'),
        ];
//        GATEWAY_USER=103161020074
//        GATEWAY_PASSWORD=E12wKp7a7vD8

        $promise = $client->post('register.do',$options);
//            ->then(
//            function ($response){
//                var_dump($response->getBody());
//                return $response->getBody();
//            }, function ($exception){
//                echo 'failed';
//                return $exception->getMessage();
//            }
//        );
        return $promise->getBody();
    }
}