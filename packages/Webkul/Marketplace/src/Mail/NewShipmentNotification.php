<?php

namespace Webkul\Marketplace\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * New Shipment Mail class
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class NewShipmentNotification extends Mailable
{
    use Queueable, SerializesModels;
    
    /**
     * The Shipment instance.
     *
     * @var Shipment
     */
    public $sellerShipment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sellerShipment)
    {
        $this->sellerShipment = $sellerShipment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->sellerShipment->order->seller->customer->email, $this->sellerShipment->order->seller->customer->name)
                ->subject(trans('marketplace::app.mail.sales.shipment.subject'))
                ->view('marketplace::emails.sales.new-shipment');
    }
}
