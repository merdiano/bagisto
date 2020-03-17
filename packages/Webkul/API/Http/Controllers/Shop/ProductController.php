<?php

namespace Webkul\API\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\Marketplace\Repositories\ProductRepository as MarketPlaceProductRepository;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\API\Http\Resources\Catalog\Product as ProductResource;

/**
 * Product controller
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class ProductController extends Controller
{
    /**
     * ProductRepository object
     *
     * @var array
     */
    protected $productRepository;

    /**
     * SellerRepository object
     *
     * @var array
     */
    protected $sellerRepository;

    protected $marketProductRepository;
    /**
     * Create a new controller instance.
     *
     * @param  Webkul\Product\Repositories\ProductRepository $productRepository
     * @param  Webkul\Marketplace\Repositories\SellerRepository $sellerRepository
     * @param  Webkul\Marketplace\Repositories\ProductRepository $marketProductRepository
     * @return void
     */
    public function __construct(ProductRepository $productRepository,
                                SellerRepository $sellerRepository,
                                MarketPlaceProductRepository $marketProductRepository)
    {
        $this->productRepository = $productRepository;
        $this->sellerRepository = $sellerRepository;
        $this->marketProductRepository = $marketProductRepository;
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ProductResource::collection($this->productRepository->getAll(request()->input('category_id')));
    }

    public function sellerProducts($url){
        $seller = $this->sellerRepository->findByUrlOrFail($url);
        return $this->marketProductRepository->findAllBySeller($seller);
    }
    /**
     * Returns a individual resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get($id)
    {
        return new ProductResource(
                $this->productRepository->findOrFail($id)
            );
    }

    /**
     * Returns product's additional information.
     *
     * @return \Illuminate\Http\Response
     */
    public function additionalInformation($id)
    {
        return response()->json([
                'data' => app('Webkul\Product\Helpers\View')->getAdditionalData($this->productRepository->findOrFail($id))
            ]);
    }

    /**
     * Returns product's additional information.
     *
     * @return \Illuminate\Http\Response
     */
    public function configurableConfig($id)
    {
        return response()->json([
                'data' => app('Webkul\Product\Helpers\ConfigurableOption')->getConfigurationConfig($this->productRepository->findOrFail($id))
            ]);
    }
}
