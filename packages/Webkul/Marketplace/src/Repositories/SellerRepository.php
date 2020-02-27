<?php

namespace Webkul\Marketplace\Repositories;

use DB;
use Illuminate\Container\Container as App;
use Webkul\Core\Eloquent\Repository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Webkul\Product\Repositories\ProductInventoryRepository;
use Webkul\Marketplace\Repositories\OrderItemRepository;
use Event;

/**
 * Seller Reposotory
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class SellerRepository extends Repository
{
    /**
     * OrderItemRepository object
     *
     * @var Object
     */
    protected $orderItemRepository;

    /**
     * ProductInventoryRepository object
     *
     * @var array
     */
    protected $productInventoryRepository;

    /**
     * Create a new repository instance.
     *
     * @param  Webkul\Marketplace\Repositories\OrderItemRepository    $orderItemRepository
     * @param  Webkul\Product\Repositories\ProductInventoryRepository $productInventoryRepository
     * @param  Illuminate\Container\Container                         $app
     * @return void
     */
    public function __construct(
        OrderItemRepository $orderItemRepository,
        ProductInventoryRepository $productInventoryRepository,
        App $app
    )
    {
        $this->orderItemRepository = $orderItemRepository;

        $this->productInventoryRepository = $productInventoryRepository;

        parent::__construct($app);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Marketplace\Contracts\Seller';
    }

    /**
     * Retrive seller from url
     *
     * @param string $url
     * @return mixed
     */
    public function findByUrlOrFail($url, $columns = null)
    {
        if ($seller = $this->findOneByField('url', $url)) {
            return $seller;
        }

        throw (new ModelNotFoundException)->setModel(
            get_class($this->model), $url
        );
    }

    /**
     * @param array $data
     * @param $id
     * @param string $attribute
     * @return mixed
     */
    public function update(array $data, $id, $attribute = "id")
    {
        Event::fire('marketplace.seller.profile.update.before', $id);

        $seller = $this->find($id);

        parent::update($data, $id);

        $this->uploadImages($data, $seller);

        $this->uploadImages($data, $seller, 'banner');

        Event::fire('marketplace.seller.profile.update.after', $seller);

        return $seller;
    }

    /**
     * Checks if customer is registered as seller or not
     *
     * @param integer $customerId
     * @return boolean
     */
    public function isSeller($customerId)
    {
        $isSeller = $this->getModel()->where('customer_id', $customerId)
            ->limit(1)
            ->select(\DB::raw(1))
            ->exists();

        return $isSeller ? $this->isSellerApproved($customerId) : false;
    }

    /**
     * Checks if seller is approved or not
     *
     * @param $customerId
     * @return boolean
     */
    public function isSellerApproved($customerId)
    {
        $isSellerApproved = $this->getModel()->where('customer_id', $customerId)
            ->where('is_approved', 1)
            ->limit(1)
            ->select(\DB::raw(1))
            ->exists();

        return $isSellerApproved ? true : false;
    }

    /**
     * @param array $data
     * @param mixed $seller
     * @return void
     */
    public function uploadImages($data, $seller, $type = "logo")
    {
        if (isset($data[$type])) {
            foreach ($data[$type] as $imageId => $image) {
                $file = $type . '.' . $imageId;
                $dir = 'seller/' . $seller->id;

                if (request()->hasFile($file)) {
                    if ($seller->{$type}) {
                        Storage::delete($seller->{$type});
                    }

                    $seller->{$type} = request()->file($file)->store($dir);
                    $seller->save();
                }
            }
        } else {
            if ($seller->{$type}) {
                Storage::delete($seller->{$type});
            }

            $seller->{$type} = null;
            $seller->save();
        }
    }

    /**
     * Returns top 4 popular sellers
     *
     * @return Collection
     */
    public function getPopularSellers()
    {
        $result = $this->getModel()
            ->leftJoin('marketplace_orders', 'marketplace_sellers.id', 'marketplace_orders.marketplace_seller_id')
            ->leftJoin('marketplace_order_items', 'marketplace_orders.id', 'marketplace_order_items.marketplace_order_id')
            ->leftJoin('order_items', 'marketplace_order_items.order_item_id', 'order_items.id')
            ->addSelect('marketplace_sellers.*')
            ->addSelect(DB::raw('SUM(qty_ordered) as total_qty_ordered'))
            ->groupBy('marketplace_sellers.id')
            ->where('marketplace_sellers.shop_title', '<>', NULL)
            // ->where('marketplace_sellers.is_approved', 0)
            ->orderBy('total_qty_ordered', 'DESC')
            ->limit(4)
            ->get();

        return $result;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        Event::fire('marketplace.seller.delete.before', $id);

        parent::delete($id);

        Event::fire('marketplace.seller.delete.after', $id);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteInventory($id)
    {
        $inventories = $this->productInventoryRepository->findWhere([
            'vendor_id' => $id
        ]);

        if (count($inventories)) {
            foreach ($inventories as $inventory) {
                if (isset ($inventory)) {
                    $this->productInventoryRepository->delete($inventory->id);
                }
            }
        }
    }
}