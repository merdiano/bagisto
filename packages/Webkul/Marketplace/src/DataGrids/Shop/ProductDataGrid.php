<?php

namespace Webkul\Marketplace\DataGrids\Shop;

use DB;
use Webkul\Ui\DataGrid\DataGrid;
use Webkul\Marketplace\Repositories\SellerRepository;

/**
 * Product Data Grid class
 *
 * @author Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class ProductDataGrid extends DataGrid
{
    /**
     * @var integer
     */
    protected $index = 'product_id';

    /**
     * @var string
     */
    protected $sortOrder = 'desc'; //asc or desc

    /**
     * SellerRepository object
     *
     * @var Object
     */
    protected $sellerRepository;

    /**
     * Create a new repository instance.
     *
     * @param  Webkul\Marketplace\Repositories\SellerRepository $sellerRepository
     * @return void
     */
    public function __construct(SellerRepository $sellerRepository)
    {
        parent::__construct();

        $this->sellerRepository = $sellerRepository;
    }

    public function prepareQueryBuilder()
    {
        $seller = $this->sellerRepository->findOneByField('customer_id', auth()->guard('customer')->user()->id);

        $queryBuilder =  DB::table('product_flat')
        ->leftJoin('products', 'product_flat.product_id', '=', 'products.id')
        ->join('marketplace_products', 'product_flat.product_id', '=', 'marketplace_products.product_id')
        ->leftJoin('marketplace_sellers', 'marketplace_products.marketplace_seller_id', '=', 'marketplace_sellers.id')
        ->leftJoin('customers', 'marketplace_sellers.customer_id', '=', 'customers.id')
        ->select('product_flat.product_id')
        ->addSelect('marketplace_products.id as marketplace_product_id', 'product_flat.product_id', 'product_flat.sku', 'product_flat.name', 'marketplace_products.price', 'product_flat.price as product_flat_price', 'marketplace_products.is_owner', 'marketplace_products.is_approved',  DB::raw('CONCAT(customers.first_name, " ", customers.last_name) as seller_name'))
        ->where('marketplace_products.marketplace_seller_id', $seller->id)
        ->where('channel', core()->getCurrentChannelCode())
        ->where('locale', app()->getLocale())
        ->distinct();

        $queryBuilder = $queryBuilder->leftJoin('product_inventories', function($qb) {
            $qb->on('product_flat.product_id', 'product_inventories.product_id')
                ->where('product_inventories.vendor_id', '<>', 0);
        });


        $queryBuilder
            ->groupBy('product_flat.product_id')
            ->addSelect(DB::raw('SUM(product_inventories.qty) as quantity'));

        $this->addFilter('sku', 'product_flat.sku');
        $this->addFilter('product_id', 'product_flat.product_id');
        $this->addFilter('price', 'product_flat.price');
        $this->addFilter('is_approved', 'marketplace_products.is_approved');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index' => 'product_id',
            'label' => trans('marketplace::app.shop.sellers.account.catalog.products.id'),
            'type' => 'number',
            'searchable' => false,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'sku',
            'label' => trans('marketplace::app.shop.sellers.account.catalog.products.sku'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'name',
            'label' => trans('marketplace::app.shop.sellers.account.catalog.products.name'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'price',
            'label' => trans('marketplace::app.shop.sellers.account.catalog.products.price'),
            'type' => 'price',
            'sortable' => true,
            'searchable' => false,
            'filterable' => true,
            'wrapper' => function($row) {
                if ($row->is_owner == 1)
                    return $row->product_flat_price;
                else
                    return $row->price;
            }
        ]);

        $this->addColumn([
            'index' => 'quantity',
            'label' => trans('marketplace::app.shop.sellers.account.catalog.products.quantity'),
            'type' => 'number',
            'sortable' => true,
            'searchable' => false,
            'filterable' => false
        ]);

        $this->addColumn([
            'index' => 'is_approved',
            'label' => trans('marketplace::app.shop.sellers.account.catalog.products.is-approved'),
            'type' => 'boolean',
            'sortable' => true,
            'searchable' => false,
            'filterable' => true,
            'wrapper' => function($row) {
                if ($row->is_approved == 1)
                    return trans('marketplace::app.shop.sellers.account.catalog.products.yes');
                else
                    return trans('marketplace::app.shop.sellers.account.catalog.products.no');
            }
        ]);
    }

    public function prepareActions() {
        $this->addAction([
            'type' => 'Edit',
            'method' => 'GET',
            'route' => 'marketplace.account.products.edit',
            'icon' => 'icon pencil-lg-icon'
        ]);

        $this->addAction([
            'type' => 'Delete',
            'method' => 'GET',
            'route' => 'marketplace.account.products.delete',
            'confirm_text' => trans('ui::app.datagrid.massaction.delete', ['resource' => 'product']),
            'icon' => 'icon trash-icon'
        ]);
    }

    public function prepareMassActions() {
        $this->addMassAction([
            'type' => 'delete',
            'label' => trans('marketplace::app.shop.sellers.account.catalog.products.delete'),
            'action' => route('marketplace.account.products.massdelete'),
            'method' => 'DELETE'
        ]);
    }
}