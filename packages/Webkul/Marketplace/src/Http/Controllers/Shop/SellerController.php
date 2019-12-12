<?php

namespace Webkul\Marketplace\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\Marketplace\Mail\ContactSellerNotification;

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
     * Display the specified resource.
     *
     * @param  string  $url
     * @return \Illuminate\Http\Response
     */
    public function show($url)
    {
        
        $seller = $this->seller->findByUrlOrFail($url);

        return view($this->_config['view'], compact('seller'));
    }

    /**
     * Send query email to seller
     *
     * @param  string  $url
     * @return \Illuminate\Http\Response
     */
    public function contact($url)
    {
        $this->validate(request(), [
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required',
            'query' => 'required',
        ]);

        $seller = $this->seller->findByUrlOrFail($url);

        try {
            Mail::send(new ContactSellerNotification($seller, request()->all()));
        } catch (\Exception $e) {}

        return response()->json([
                'success' => true,
                'message' => 'Email has been sent successfully. Seller will contact you as soon as possible.'
            ]);
    }

    /**
     * Check if shop url available or not
     *
     * @return \Illuminate\Http\Response
     */
    public function checkShopUrl()
    {
        $seller = $this->seller->findOneByField([
                'url' => trim(request()->input('url'))
            ]);

        return response()->json([
                'available' => $seller ? false : true
            ]);
    }
}