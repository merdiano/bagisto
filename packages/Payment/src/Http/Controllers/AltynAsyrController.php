<?php

namespace Payment\Http\Controllers;

use Webkul\Checkout\Facades\Cart;
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

    /**
     * @var AltynAsyr object
     */
    protected $altynAsyr;

    public function __construct(OrderRepository $orderRepository, AltynAsyr $altynAsyr)
    {
        $this->orderRepository = $orderRepository;
        $this->altynAsyr = $altynAsyr;
    }

    /**
     * Redirects to payment gateway
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function redirect(){
        // register order to payment gateway
        try{
            $result =  $this->altynAsyr->registerOrder();
            if($result['errorCode'] == 0){
//                dd($result);
                $this->altynAsyr->registerOrderId($result['orderId']);
                return redirect($result['formUrl']);
            }
            else{//if already registered or otkazana w dostupe
                //todo log
                session()->flash('error', $result['errorMessage']);
            }

        }catch (\Exception $exception){
            //todo Check exception if not connection excepion redirect to login ore somewhere if session expired
            session()->flash('error', $exception->getMessage());//'Bank bilen aragatnaşykda säwlik ýüze çykdy. Ýene birsalymdan täzeden synanşyp görmegiňizi haýş edýäris!');
        }

        return redirect()->route('shop.checkout.cart.index');
    }

    /**
     * Success payment
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function success(){
        try {
            $result = $this->altynAsyr->getOrderStatus();

            if ($result['ErrorCode'] == 0) {
                if ($result['OrderStatus'] == 2) {
                    $order = $this->orderRepository->create(Cart::prepareDataForOrder());

                    Cart::deActivateCart();

                    session()->flash('order', $order);

                    return redirect()->route('shop.checkout.success');
                } else {
                    return view('payment::order-status')->with('cart', $this->altynAsyr->getCart());
                }

            } else {
                $message = "Hasabyňyzda ýeterli pul ýok. Ýa-da kart maglumatlaryňyz dogry däl.";
                session()->flash('error', $message);
            }
        }
        catch (\Exception $exception){
            //todo check Exception type if it is not connection exception display exception message else bank bn aragat...
            $message = 'Bank bilen aragatnaşykda säwlik ýüze çykdy. Ýene birsalymdan täzeden synanşyp görmegiňizi haýş edýäris!';
//            dd($exception);
            session()->flash('error',$message);
        }
        return redirect()->route('shop.checkout.cart.index');
    }

    /**
     * Cancel payment from gateway
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(){
        $message = "Hasabyňyzda ýeterli pul ýok. Ýa-da kart maglumatlaryňyz dogry däl.";
        session()->flash('error', $message);

        return redirect()->route('shop.checkout.cart.index');
    }


    public function status(){

        return view('payment::order-status');
    }
}
