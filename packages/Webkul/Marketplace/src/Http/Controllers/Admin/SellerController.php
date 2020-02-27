<?php

namespace Webkul\Marketplace\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\Marketplace\Mail\SellerApprovalNotification;
use Webkul\Marketplace\Repositories\ProductRepository as SellerProduct;
use Webkul\Product\Repositories\ProductRepository as Product;

/**
 * Admin Seller Controller
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class SellerController extends Controller
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
    protected $sellerRepository;

    /**
     * ProductRepository object
     *
     * @var array
     */
    protected $sellerProduct;

    /**
     * ProductRepository object
     *
     * @var array
     */
    protected $product;

    /**
     * Create a new controller instance.
     *
     * @param  Webkul\Marketplace\Repositories\SellerRepository   $sellerRepository
     * @param  Webkul\Marketplace\Repositories\ProductRepository  $sellerProduct
     * @param  Webkul\Product\Repositories\ProductRepository      $product
     * @return void
     */
    public function __construct(SellerRepository $sellerRepository, SellerProduct $sellerProduct,  Product $product)
    {
        $this->_config = request('_config');

        $this->sellerRepository = $sellerRepository;

        $this->sellerProduct = $sellerProduct;

        $this->product = $product;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Mixed
     */
    public function index()
    {
        return view($this->_config['view']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->sellerRepository->delete($id);

        session()->flash('success', trans('marketplace::app.admin.response.delete-success', ['name' => 'Seller']));

        return redirect()->back();
    }

    /**
     * Mass Delete the sellers
     *
     * @return response
     */
    public function massDestroy()
    {
        $sellerIds = explode(',', request()->input('indexes'));

        foreach ($sellerIds as $sellerId) {
            $this->sellerRepository->delete($sellerId);
        }

        session()->flash('success', trans('marketplace::app.admin.sellers.mass-delete-success'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Mass updates the sellers
     *
     * @return response
     */
    public function massUpdate()
    {
        $data = request()->all();

        if (! isset($data['massaction-type']) || !$data['massaction-type'] == 'update') {
            return redirect()->back();
        }

        $sellerIds = explode(',', $data['indexes']);

        foreach ($sellerIds as $sellerId) {
            $this->sellerRepository->update([
                    'is_approved' => $data['update-options']
                ], $sellerId);

            if ($data['update-options']) {
                $seller = $this->sellerRepository->find($sellerId);

                try {
                    Mail::send(new SellerApprovalNotification($seller));
                } catch (\Exception $e) {
                }
            }
        }

        session()->flash('success', trans('marketplace::app.admin.sellers.mass-update-success'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $sellerId
     * @return \Illuminate\Http\Response
     */
    public function search($id)
    {
        if (request()->ajax()) {
            $results = [];

            foreach ($this->sellerProduct->searchProducts(request()->input('query')) as $row) {
                $results[] = [
                        'id' => $row->product_id,
                        'sku' => $row->sku,
                        'name' => $row->name,
                        'price' => core()->convertPrice($row->price),
                        'formated_price' => core()->currency(core()->convertPrice($row->price)),
                        'base_image' => $row->product->base_image_url,
                    ];
            }

            return response()->json($results);
        } else {
            return view($this->_config['view'], compact('id'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $sellerId,  $productId
     * @return \Illuminate\Http\Response
     */
    public function assignProduct($sellerId, $productId)
    {
        $product = $this->sellerProduct->findOneWhere([
            'product_id' => $productId,
            'marketplace_seller_id' => $sellerId
        ]);

        if ($product) {
            session()->flash('error', 'You are already selling this product..');

            return redirect()->route('admin.marketplace.sellers.index');
        }

        $baseProduct = $this->product->find($productId);

        $inventorySources = core()->getCurrentChannel()->inventory_sources;

        return view($this->_config['view'], compact('baseProduct', 'inventorySources'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  int  $sellerId,  $productId
     * @return \Illuminate\Http\Response
     */
    public function saveAssignProduct($sellerId, $productId)
    {
        $this->validate(request(), [
            'condition' => 'required',
            'description' => 'required'
        ]);

        $data = array_merge(request()->all(), [
            'product_id' => $productId,
            'is_owner' => 0,
            'seller_id' => $sellerId
        ]);

        $product = $this->sellerProduct->createAssign($data);

        session()->flash('success', 'Product created successfully.');

        return redirect()->route($this->_config['redirect']);
    }
}