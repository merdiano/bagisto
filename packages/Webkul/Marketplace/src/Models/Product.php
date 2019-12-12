<?php

namespace Webkul\Marketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Marketplace\Contracts\Product as ProductContract;
use Webkul\Product\Models\Product as BaseProduct;

class Product extends Model implements ProductContract
{
    protected $table = 'marketplace_products';

    protected $fillable = ['condition', 'description', 'price', 'marketplace_seller_id', 'parent_id', 'product_id', 'is_owner', 'is_approved'];

    /**
     * Get the product that belongs to the product.
     */
    public function product()
    {
        return $this->belongsTo(BaseProduct::class);
    }

    /**
     * Get the product that belongs to the seller.
     */
    public function seller()
    {
        return $this->belongsTo(SellerProxy::modelClass(), 'marketplace_seller_id');
    }

    /**
     * Get the product variants that owns the product.
     */
    public function variants()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Get the product that owns the product.
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * The images that belong to the product.
     */
    public function images()
    {
        return $this->hasMany(ProductImageProxy::modelClass(), 'marketplace_product_id');
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function isSaleable()
    {
        if (! $this->product->status && $this->is_approved)
            return false;

        if ($this->haveSufficientQuantity(1))
            return true;

        return false;
    }

    /**
     * @param integer $qty
     *
     * @return bool
     */
    public function haveSufficientQuantity($qty)
    {
        $total = 0;

        $channelInventorySourceIds = core()->getCurrentChannel()
                ->inventory_sources()
                ->where('status', 1)
                ->pluck('id');

        foreach ($this->product->inventories as $inventory) {
            if (is_numeric($index = $channelInventorySourceIds->search($inventory->inventory_source_id)) && $this->seller->id == $inventory->vendor_id) {
                $total += $inventory->qty;
            }
        }

        if (!$total) {
            return false;
        }

        $orderedInventory = $this->product->ordered_inventories()
                ->where('channel_id', core()->getCurrentChannel()->id)
                ->first();

        if ($orderedInventory) {
            $total -= $orderedInventory->qty;
        }

        return $qty <= $total ? true : false;
    }
}