<?php

namespace Webkul\Marketplace\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Seller registration mail to Admin
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class NewSellerNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The seller instance.
     *
     * @var Seller
     */
    public $seller;

    /**
     * The admin instance.
     *
     * @var Admin
     */
    public $admin;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($seller, $admin)
    {
        $this->seller = $seller;

        $this->admin = $admin;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->admin->email)
                ->subject(trans('marketplace::app.mail.seller.regisration.subject'))
                ->view('marketplace::emails.seller.register');
    }
}