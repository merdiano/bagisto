<?php

namespace Webkul\Marketplace\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * New Invoice Mail class
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class NewInvoiceNotification extends Mailable
{
    use Queueable, SerializesModels;
    
    /**
     * The Invoice instance.
     *
     * @var Invoice
     */
    public $sellerInvoice;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sellerInvoice)
    {
        $this->sellerInvoice = $sellerInvoice;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->sellerInvoice->invoice->order->customer_email, $this->sellerInvoice->invoice->order->customer_full_name)
                ->subject(trans('marketplace::app.mail.sales.invoice.subject'))
                ->view('marketplace::emails.sales.new-invoice');
    }
}
