<?php

namespace Webkul\Marketplace\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Product Approval Mail class
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class ProductApprovalNotification extends Mailable
{
    use Queueable, SerializesModels;
    
    /**
     * The Product instance.
     *
     * @var Product
     */
    public $sellerProduct;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sellerProduct)
    {
        $this->sellerProduct = $sellerProduct;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->sellerProduct->seller->customer->email, $this->sellerProduct->seller->customer->name)
                ->subject(trans('marketplace::app.mail.product.subject'))
                ->view('marketplace::emails.product.approval');
    }
}
