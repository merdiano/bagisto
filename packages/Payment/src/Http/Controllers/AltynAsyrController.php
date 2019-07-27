<?php

namespace Payment\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Payment\CardPayment\AltynAsyr;
use Webkul\Sales\Repositories\OrderRepository;

class AltynAsyrController extends Controller
{
    /**
     * OrderRepository object
     *
     * @var array
     */
    protected $orderRepository;
    protected $altynAsyr;

    public function __construct(OrderRepository $orderRepository, AltynAsyr $altynAsyr)
    {
        $this->orderRepository = $orderRepository;
        $this->altynAsyr = $altynAsyr;
    }

    public function redirect(){
        // register order to payment gateway
//        $altynAsyr = app('Payment\CardPayment\AltynAsyr');
        try{
            $result =  $this->altynAsyr->registerOrder();
            if($result['errorCode'] == 0){
//                dd($result);
                $this->altynAsyr->registerOrderId($result['orderId']);
                return redirect($result['formUrl']);
            }
            else{
                $message = $result['errorMessage'];
            }

        }catch (\Exception $exception){
            $message = $exception->getMessage();
        }
        return view('payment::registration_failed',compact('message'));
    }

    public function success($cart_id){
        try{
            $result = $this->altynAsyr->getOrderStatus();
        }
        catch (\Exception $exception){

        }
    }

    public function cancel(){

    }
}
