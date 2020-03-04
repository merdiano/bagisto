<?php

namespace Webkul\Marketplace\Listeners;

use Illuminate\Support\Facades\Mail;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\User\Repositories\AdminRepository;
use Webkul\Marketplace\Mail\SellerWelcomeNotification;
use Webkul\Marketplace\Mail\SellerApprovalNotification;
use Webkul\Marketplace\Mail\NewSellerNotification;

/**
 * Customer event handler
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class Customer
{
    /**
     * SellerRepository object
     *
     * @var Seller
    */
    protected $seller;

    /**
     * AdminRepository object
     *
     * @var Seller
    */
    protected $admin;

    /**
     * Create a new customer event listener instance.
     *
     * @param  Webkul\Marketplace\Repositories\SellerRepository $seller
     * @param  Webkul\User\Repositories\AdminRepository  $admin
     * @return void
     */
    public function __construct(SellerRepository $seller, AdminRepository $admin)
    {
        $this->seller = $seller;

        $this->admin = $admin;
    }

    /**
     * Register seller if customer requested
     *
     * @param mixed $customer
     */
    public function registerSeller($customer)
    {
        $admin = $this->admin->findOneWhere(['role_id' => 1]);

        if (request()->input('want_to_be_seller') && $url = request()->input('url')) {
            $seller = $this->seller->findOneByField([
                    'url' => $url
                ]);

            if (! $seller) {
                $data = [
                        'customer_id' => $customer->id,
                        'url' => $url,
                        'is_approved' => core()->getConfigData('marketplace.settings.general.seller_approval_required') ? 0 : 1
                    ];

                $seller = $this->seller->create($data);

                try {
                    if ($seller->is_approved) {
                        Mail::send(new SellerApprovalNotification($seller));
                    } else {
                        Mail::send(new SellerWelcomeNotification($seller));

                        Mail::send(new NewSellerNotification($seller, $admin));
                    }
                } catch (\Exception $e) {}

            }
        }
    }

    /**
     * Delete inventory of seller after delete
     *
     * @param mixed $id
     */
    public function afterSellerDelete($id) {
        $this->seller->deleteInventory($id);
    }
}
