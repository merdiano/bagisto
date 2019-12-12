<?php

namespace Webkul\Marketplace\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Webkul\Marketplace\Repositories\ProductRepository;
use Webkul\Marketplace\Mail\ProductApprovalNotification;

/**
 * Admin Product Controller
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
     * ProductRepository object
     *
     * @var array
    */
    protected $productRepository;

    /**
     * Create a new controller instance.
     *
     * @param  Webkul\Marketplace\Repositories\ProductRepository $productRepository
     * @return void
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->_config = request('_config');

        $this->productRepository = $productRepository;
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
        $this->productRepository->delete($id);

        session()->flash('success', trans('marketplace::app.admin.response.delete-success', ['name' => 'Product']));

        return redirect()->back();
    }

    /**
     * Mass Delete the products
     *
     * @return response
     */
    public function massDestroy()
    {
        $productIds = explode(',', request()->input('indexes'));

        foreach ($productIds as $productId) {
            $product = $this->productRepository->find($productId);


            if ($product) {
                $this->productRepository->delete($product->id);
            }
        }

        session()->flash('success', trans('marketplace::app.admin.products.mass-delete-success'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Mass updates the products
     *
     * @return response
     */
    public function massUpdate()
    {
        $data = request()->all();

        if (! isset($data['massaction-type']) || !$data['massaction-type'] == 'update') {
            return redirect()->back();
        }

        $productIds = explode(',', $data['indexes']);

        foreach ($productIds as $productId) {
            $sellerProduct = $this->productRepository->find($productId);

            if ($sellerProduct) {
                $product = $sellerProduct->product;

                $sellerProduct->update([
                        'is_approved' => $data['update-options']
                    ]);

                if ($data['update-options']) {
                    try {
                        Mail::send(new ProductApprovalNotification($sellerProduct));
                    } catch (\Exception $e) {

                    }
                }
            }
        }

        session()->flash('success', trans('marketplace::app.admin.products.mass-update-success'));

        return redirect()->route($this->_config['redirect']);
    }
}