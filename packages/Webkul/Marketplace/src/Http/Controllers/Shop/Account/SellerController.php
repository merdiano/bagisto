<?php

namespace Webkul\Marketplace\Http\Controllers\Shop\Account;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Webkul\Marketplace\Http\Controllers\Shop\Controller;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\Marketplace\Http\Requests\SellerForm;

/**
 * Marketplace seller page controller
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
    protected $seller;

    /**
     * Create a new controller instance.
     *
     * @param  Webkul\Marketplace\Repositories\SellerRepository $seller
     * @return void
     */
    public function __construct(SellerRepository $seller)
    {
        $this->_config = request('_config');

        $this->seller = $seller;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $seller = $this->seller->findOneByField('customer_id', auth()->guard('customer')->user()->id);

        if ($seller && $seller->is_approved) {
            return redirect()->route('marketplace.account.seller.edit');
        }

        return view($this->_config['view'], compact('seller'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'url' => ['required', 'unique:marketplace_sellers,url', new \Webkul\Core\Contracts\Validations\Slug]
        ]);

        $data = request()->all();

        $data['customer_id'] = auth()->guard('customer')->user()->id;

        if (! core()->getConfigData('marketplace.settings.general.seller_approval_required')) {
            $data['is_approved'] = 1;

            session()->flash('success', 'Your seller account created successfully.');
        } else {
            session()->flash('success', 'Your request to become seller is successfully raised.');
        }

        $this->seller->create($data);

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function edit()
    {
        $isSeller = $this->seller->isSeller(auth()->guard('customer')->user()->id);

        if (! $isSeller) {
            return redirect()->route('marketplace.account.seller.create');
        }

        $seller = $this->seller->findOneByField('customer_id', auth()->guard('customer')->user()->id);

        return view($this->_config['view'], compact('seller'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Webkul\Marketplace\Http\Requests\SellerForm $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SellerForm $request, $id)
    {
        $isSeller = $this->seller->isSeller(auth()->guard('customer')->user()->id);

        if (! $isSeller) {
            return redirect()->route('marketplace.account.seller.create');
        }

        $this->seller->update(request()->all(), $id);

        session()->flash('success', 'Your profile saved successfully.');

        return redirect()->route($this->_config['redirect']);
    }
}