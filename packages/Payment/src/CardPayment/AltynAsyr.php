<?php
/**
 * Created by PhpStorm.
 * User: merdan
 * Date: 7/24/2019
 * Time: 16:48
 */

namespace Payment\CardPayment;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Webkul\Payment\Payment\Payment;


class AltynAsyr extends Payment
{
    protected $code  = 'altynasyr';

    public function getRedirectUrl():String
    {
        return route('paymentmethod.altynasyr.redirect');
    }

    private function getApiClient():Client{
        return new Client([
            'base_uri' => $this->getConfigData('api_url'),
            'connect_timeout' => 10,
            'timeout' => 10,
            'verify' => true,
        ]);
    }

    public function isRegistered(){
        $payment = $this->getCart()->payment;
        return (!empty($payment) && !empty($payment->orderId));
    }

    public function registerOrder(){

        $cart = $this->getCart();
        $lifeTime = config('session.lifetime',10);//10 minutes

        $client = $this->getApiClient();

        $params =[
            'form_params' => [
                'userName' => $this->getConfigData('business_account'),//'103161020074',
                'password' => $this->getConfigData('account_password'),//'E12wKp7a7vD8',
                'sessionTimeoutSecs' => $lifeTime * 30, //(600 sec)
                'orderNumber' =>$cart->id . Carbon::now()->timestamp,
                'currency' => 934,
                'language' => 'ru',
                'description'=> "bagisto multivendor {$cart->grand_total}m.",
                'amount' =>$cart->grand_total * 100,// amount w kopeykah
                'returnUrl' => route('paymentmethod.altynasyr.success'),
                'failUrl' => route('paymentmethod.altynasyr.cancel')
            ],
        ];

        return json_decode($client->post('register.do',$params)->getBody(),true);

    }

    public function registerOrderId($orderId){
        $payment = $this->getCart()->payment;
        $payment->order_id = $orderId;
//        dd($payment);
//        $payment->paymentFormUrl = $formUrl;
        $payment->save();
    }

    public function getOrderStatus(){
        $client = $this->getApiClient();
        $payment = $this->getCart()->payment;

        $params = [
            'form_params' => [
                'userName' => $this->getConfigData('business_account'),//'103161020074',
                'password' => $this->getConfigData('account_password'),//'E12wKp7a7vD8',
                'orderId' => $payment->order_id,
            ]
        ];

        return json_decode($client->post('getOrderStatus.do',$params)->getBody(),true);

    }
}