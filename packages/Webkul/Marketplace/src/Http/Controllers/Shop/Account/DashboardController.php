<?php

namespace Webkul\Marketplace\Http\Controllers\Shop\Account;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Webkul\Marketplace\Http\Controllers\Shop\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\Marketplace\Repositories\OrderRepository;
use Webkul\Marketplace\Repositories\OrderItemRepository;
use Webkul\Product\Repositories\ProductInventoryRepository;

/**
 * Dashboard controller
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $_config;

    /**
     * SellerRepository object
     *
     * @var array
     */
    protected $sellerRepository;

    /**
     * Seller object
     *
     * @var array
     */
    protected $seller;

    /**
     * OrderRepository object
     *
     * @var array
     */
    protected $orderRepository;

    /**
     * OrderItemRepository object
     *
     * @var array
     */
    protected $orderItemRepository;

    /**
     * ProductInventoryRepository object
     *
     * @var array
     */
    protected $productInventoryRepository;

    /**
     * string object
     *
     * @var array
     */
    protected $startDate;

    /**
     * string object
     *
     * @var array
     */
    protected $lastStartDate;

    /**
     * string object
     *
     * @var array
     */
    protected $endDate;

    /**
     * string object
     *
     * @var array
     */
    protected $lastEndDate;

    /**
     * Create a new controller instance.
     *
     * @param  Webkul\Marketplace\Repositories\SellerRepository       $sellerRepository
     * @param  Webkul\Marketplace\Repositories\OrderRepository        $orderRepository
     * @param  Webkul\Marketplace\Repositories\OrderItemRepository    $orderItemRepository
     * @param  Webkul\Product\Repositories\ProductInventoryRepository $productInventoryRepository
     * @return void
     */
    public function __construct(
        SellerRepository $sellerRepository,
        OrderRepository $orderRepository,
        OrderItemRepository $orderItemRepository,
        ProductInventoryRepository $productInventoryRepository
    )
    {
        $this->_config = request('_config');

        $this->sellerRepository = $sellerRepository;

        $this->orderRepository = $orderRepository;

        $this->orderItemRepository = $orderItemRepository;

        $this->productInventoryRepository = $productInventoryRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $isSeller = $this->sellerRepository->isSeller(auth()->guard('customer')->user()->id);

        if (! $isSeller) {
            return redirect()->route('marketplace.account.seller.create');
        }

        $this->setStartEndDate();

        $this->seller = $this->sellerRepository->findOneByField('customer_id', auth()->guard('customer')->user()->id);

        $statistics = [
            'total_orders' =>  [
                'previous' => $previous = $this->orderRepository->scopeQuery(function($query) {
                        return $query->where('marketplace_orders.marketplace_seller_id', $this->seller->id)
                            ->where('marketplace_orders.created_at', '>=', $this->lastStartDate)
                            ->where('marketplace_orders.created_at', '<=', $this->lastEndDate);
                    })->count(),
                'current' => $current = $this->orderRepository->scopeQuery(function($query) {
                        return $query->where('marketplace_orders.marketplace_seller_id', $this->seller->id)
                            ->where('marketplace_orders.created_at', '>=', $this->startDate)
                            ->where('marketplace_orders.created_at', '<=', $this->endDate);
                    })->count(),
                'progress' => $this->getPercentageChange($previous, $current)
            ],
            'total_sales' =>  [
                'previous' => $previous = $this->orderRepository->scopeQuery(function($query) {
                        return $query->where('marketplace_orders.marketplace_seller_id', $this->seller->id)
                            ->where('marketplace_orders.created_at', '>=', $this->lastStartDate)
                            ->where('marketplace_orders.created_at', '<=', $this->lastEndDate);
                    })->sum('base_seller_total'),
                'current' => $current = $this->orderRepository->scopeQuery(function($query) {
                        return $query->where('marketplace_orders.marketplace_seller_id', $this->seller->id)
                            ->where('marketplace_orders.created_at', '>=', $this->startDate)
                            ->where('marketplace_orders.created_at', '<=', $this->endDate);
                    })->sum('base_seller_total') - $this->orderRepository->scopeQuery(function($query) {
                        return $query->where('marketplace_orders.marketplace_seller_id', $this->seller->id)
                            ->where('marketplace_orders.created_at', '>=', $this->startDate)
                            ->where('marketplace_orders.created_at', '<=', $this->endDate);
                    })->sum('base_grand_total_refunded') + $this->orderRepository->scopeQuery(function($query) {
                        return $query->where('marketplace_orders.marketplace_seller_id', $this->seller->id)
                            ->where('marketplace_orders.created_at', '>=', $this->startDate)
                            ->where('marketplace_orders.created_at', '<=', $this->endDate);
                    })->sum('base_commission_invoiced'),
                'progress' => $this->getPercentageChange($previous, $current)
            ],
            'avg_sales' =>  [
                'previous' => $previous = $this->orderRepository->scopeQuery(function($query) {
                        return $query->where('marketplace_orders.marketplace_seller_id', $this->seller->id)
                            ->where('marketplace_orders.created_at', '>=', $this->lastStartDate)
                            ->where('marketplace_orders.created_at', '<=', $this->lastEndDate);
                    })->avg('base_seller_total'),
                'current' => $current = $this->orderRepository->scopeQuery(function($query) {
                        return $query->where('marketplace_orders.marketplace_seller_id', $this->seller->id)
                            ->where('marketplace_orders.created_at', '>=', $this->startDate)
                            ->where('marketplace_orders.created_at', '<=', $this->endDate);
                    })->avg('base_seller_total') - $this->orderRepository->scopeQuery(function($query) {
                        return $query->where('marketplace_orders.marketplace_seller_id', $this->seller->id)
                            ->where('marketplace_orders.created_at', '>=', $this->startDate)
                            ->where('marketplace_orders.created_at', '<=', $this->endDate);
                    })->avg('base_grand_total_refunded') + $this->orderRepository->scopeQuery(function($query) {
                        return $query->where('marketplace_orders.marketplace_seller_id', $this->seller->id)
                            ->where('marketplace_orders.created_at', '>=', $this->startDate)
                            ->where('marketplace_orders.created_at', '<=', $this->endDate);
                    })->avg('base_commission_invoiced'),
                'progress' => $this->getPercentageChange($previous, $current)
            ],
            'top_selling_products' => $this->getTopSellingProducts(),
            'customer_with_most_sales' => $this->getCustomerWithMostSales(),
            'stock_threshold' => $this->getStockThreshold(),
        ];

        foreach (core()->getTimeInterval($this->startDate, $this->endDate) as $interval) {
            $statistics['sale_graph']['label'][] = $interval['start']->format('d M');

            $total = $this->orderRepository->scopeQuery(function($query) use($interval) {
                        return $query->where('marketplace_orders.marketplace_seller_id', $this->seller->id)
                            ->where('marketplace_orders.created_at', '>=', $interval['start'])
                            ->where('marketplace_orders.created_at', '<=', $interval['end']);
                    })->sum('base_seller_total');

            $statistics['sale_graph']['total'][] = $total;
            $statistics['sale_graph']['formated_total'][] = core()->formatBasePrice($total);
        }

        return view($this->_config['view'], compact('statistics'))->with(['startDate' => $this->startDate, 'endDate' => $this->endDate]);
    }

    /**
     * Return stock threshold.
     *
     * @return mixed
     */
    public function getStockThreshold()
    {
        return $this->productInventoryRepository->getModel()
            ->leftJoin('products', 'product_inventories.product_id', 'products.id')
            ->leftJoin('marketplace_products', 'products.id', 'marketplace_products.product_id')
            ->select(DB::raw('SUM(qty) as total_qty'))
            ->addSelect('product_inventories.product_id')
            ->where('products.type', '!=', 'configurable')
            ->where('marketplace_products.marketplace_seller_id', $this->seller->id)
            ->where('product_inventories.vendor_id', $this->seller->id)
            ->groupBy('product_id')
            ->orderBy('total_qty', 'ASC')
            ->limit(5)
            ->get();
    }

    /**
     * Returns top selling products
     * @return mixed
     */
    public function getTopSellingProducts()
    {
        return $this->orderItemRepository->getModel()
            ->leftJoin('order_items', 'marketplace_order_items.order_item_id', 'order_items.id')
            ->leftJoin('marketplace_orders', 'marketplace_order_items.marketplace_order_id', 'marketplace_orders.id')
            ->select(DB::raw('SUM(qty_ordered) as total_qty_ordered'))
            ->addSelect('order_items.id', 'product_id', 'product_type', 'name')
            ->where('order_items.created_at', '>=', $this->startDate)
            ->where('order_items.created_at', '<=', $this->endDate)
            ->where('marketplace_orders.marketplace_seller_id', $this->seller->id)
            ->whereNull('order_items.parent_id')
            ->groupBy('product_id')
            ->orderBy('total_qty_ordered', 'DESC')
            ->limit(5)
            ->get();
    }

    /**
     * Returns top selling products
     *
     * @return mixed
     */
    public function getCustomerWithMostSales()
    {
        return $this->orderRepository->getModel()
            ->leftJoin('orders', 'marketplace_orders.order_id', 'orders.id')
            ->select(DB::raw('SUM(marketplace_orders.base_grand_total) as total_base_grand_total'))
            ->addSelect(DB::raw('COUNT(marketplace_orders.id) as total_orders'))
            ->addSelect('orders.id', 'orders.customer_id', 'orders.customer_email', DB::raw('CONCAT(orders.customer_first_name, " ", orders.customer_last_name) as customer_full_name'))
            ->where('marketplace_orders.created_at', '>=', $this->startDate)
            ->where('marketplace_orders.created_at', '<=', $this->endDate)
            ->where('marketplace_orders.marketplace_seller_id', $this->seller->id)
            ->groupBy('orders.customer_email')
            ->orderBy('total_base_grand_total', 'DESC')
            ->limit(5)
            ->get();
    }

    public function getPercentageChange($previous, $current)
    {
        if (! $previous)
            return $current ? 100 : 0;

        return ($current - $previous) / $previous * 100;
    }

    /**
     * Sets start and end date
     *
     * @return void
     */
    public function setStartEndDate()
    {
        $this->startDate = request()->get('start')
            ? Carbon::createFromTimeString(request()->get('start') . " 00:00:01")
            : Carbon::createFromTimeString(Carbon::now()->subDays(30)->format('Y-m-d') . " 00:00:01");

        $this->endDate = request()->get('end')
            ? Carbon::createFromTimeString(request()->get('end') . " 23:59:59")
            : Carbon::now();

        if ($this->endDate > Carbon::now())
            $this->endDate = Carbon::now();

        $this->lastStartDate = clone $this->startDate;
        $this->lastEndDate = clone $this->startDate;

        $this->lastStartDate->subDays($this->startDate->diffInDays($this->endDate));
    }
}