<?php

namespace Webkul\Marketplace\Listeners;

use Illuminate\Support\Facades\Mail;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\Marketplace\Repositories\ProductRepository;
use Cart as CartFacade;

/**
 * Cart event handler
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class Cart
{
    /**
     * SellerRepository object
     *
     * @var Seller
    */
    protected $sellerRepository;

    /**
     * ProductRepository object
     *
     * @var Product
    */
    protected $productRepository;

    /**
     * Create a new customer event listener instance.
     *
     * @param  Webkul\Marketplace\Repositories\SellerRepository  $sellerRepository
     * @param  Webkul\Marketplace\Repositories\ProductRepository $productRepository
     * @return void
     */
    public function __construct(
        SellerRepository $sellerRepository,
        ProductRepository $productRepository
    )
    {
        $this->sellerRepository = $sellerRepository;

        $this->productRepository = $productRepository;
    }

    /**
     * Product added to the cart
     *
     * @param mixed $cartItem
     */
    public function cartItemAddBefore($productId)
    {
        $data = request()->all();

        if (isset($data['seller_info']) && !$data['seller_info']['is_owner']) {
            $sellerProduct = $this->productRepository->find($data['seller_info']['product_id']);
        } else {
            if (isset($data['selected_configurable_option'])) {
                $sellerProduct = $this->productRepository->findOneWhere([
                        'product_id' => $data['selected_configurable_option'],
                        'is_owner' => 1
                    ]);
            } else {
                $sellerProduct = $this->productRepository->findOneWhere([
                        'product_id' => $productId,
                        'is_owner' => 1
                    ]);
            }
        }

        if (!$sellerProduct) {
            return;
        }

        if (! isset($data['quantity']))
            $data['quantity'] = 1;

        if ($cart = CartFacade::getCart()) {
            $cartItem = $cart->items()->where('product_id', $sellerProduct->product_id)->first();

            if ($cartItem) {
                if (!$sellerProduct->haveSufficientQuantity($data['quantity']))
                    throw new \Exception('Requested quantity not available.');

                $quantity = $cartItem->quantity + $data['quantity'];
            } else {
                $quantity = $data['quantity'];
            }
        } else {
            $quantity = $data['quantity'];
        }

        if (!$sellerProduct->haveSufficientQuantity($quantity)) {
            throw new \Exception('Requested quantity not available.');
        }
    }

    /**
     * Product added to the cart
     *
     * @param mixed $cartItem
     */
    public function cartItemAddAfter($cartItem)
    {
        if (isset($cartItem->additional['seller_info']) && !$cartItem->additional['seller_info']['is_owner']) {
            $product = $this->productRepository->find($cartItem->additional['seller_info']['product_id']);
            if ($product) {
                $cartItem->price = core()->convertPrice($product->price);
                $cartItem->base_price = $product->price;
                $cartItem->custom_price = $product->price;
                $cartItem->total = core()->convertPrice($product->price * $cartItem->quantity);
                $cartItem->base_total = $product->price * $cartItem->quantity;

                $cartItem->save();
            } else {
                $cartItem->custom_price = NULL;
            }

            $cartItem->save();
        } else {
            $cartItem->custom_price = NULL;

            $cartItem->save();
        }
    }
}
