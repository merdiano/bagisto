<?php

namespace Webkul\Marketplace\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\Marketplace\Repositories\ProductRepository;
use Webkul\Product\Repositories\ProductRepository as BaseProductRepository;

/**
 * Marketplace product controller
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class ProductController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * SellerRepository object
     *
     * @var array
    */
    protected $seller;

    /**
     * ProductRepository object
     *
     * @var array
    */
    protected $product;

    /**
     * ProductRepository object
     *
     * @var array
    */
    protected $baseProduct;

    /**
     * Create a new controller instance.
     *
     * @param  Webkul\Marketplace\Repositories\SellerRepository  $seller
     * @param  Webkul\Marketplace\Repositories\ProductRepository $product
     * @param  Webkul\Product\Repositories\ProductRepository     $baseProduct
     * @return void
     */
    public function __construct(
        SellerRepository $seller,
        ProductRepository $product,
        BaseProductRepository $baseProduct
    )
    {
        $this->_config = request('_config');

        $this->seller = $seller;

        $this->product = $product;

        $this->baseProduct = $baseProduct;
    }

    /**
     * Method to populate the seller product page which will be populated.
     *
     * @param  string  $url
     * @return Mixed
     */
    public function index($url)
    {
        $seller = $this->seller->findByUrlOrFail($url);
        
        return view($this->_config['view'], compact('seller'));
    }

    /**
     * Product offers by sellers
     *
     * @param  integer $id
     * @return Mixed
     */
    public function offers($id)
    {
        $product = $this->baseProduct->findOrFail($id);

        if ($product->type == 'configurable') {
            session()->flash('error', trans('shop::app.checkout.cart.integrity.missing_options'));

            return redirect()->route('shop.products.index', ['slug' => $product->url_key]);
        }

        return view($this->_config['view'], compact('product'));
    }
}