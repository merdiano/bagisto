<?php

namespace Webkul\Marketplace\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * New Order Mail class
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class NewOrderNotification extends Mailable
{
    use Queueable, SerializesModels;
    
    /**
     * The order instance.
     *
     * @var Order
     */
    public $sellerOrder;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sellerOrder)
    {
        $this->sellerOrder = $sellerOrder;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->sellerOrder->seller->customer->email, $this->sellerOrder->seller->customer->name)
                ->subject(trans('marketplace::app.mail.sales.order.subject'))
                ->view('marketplace::emails.sales.new-order');
    }
}
