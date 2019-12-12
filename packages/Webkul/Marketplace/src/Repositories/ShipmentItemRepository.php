<?php

namespace Webkul\Marketplace\Repositories;

use Webkul\Core\Eloquent\Repository;

/**
 * Seller Shipment Reposotory
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class ShipmentItemRepository extends Repository
{
    protected $guarded = ['id', 'child', 'created_at', 'updated_at'];
    
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Marketplace\Contracts\ShipmentItem';
    }

    /**
     * @param array $data
     * @return void
     */
    public function updateProductInventory($data)
    {
        $orderedInventory = $data['product']->ordered_inventories()
                ->where('channel_id', $data['shipment']->order->channel->id)
                ->first();
                
        if ($orderedInventory) {
            if (($orderedQty = $orderedInventory->qty - $data['qty']) < 0) {
                $orderedQty = 0;
            }
                
            $orderedInventory->update([
                    'qty' => $orderedQty
                ]);
        } else {
            $data['product']->ordered_inventories()->create([
                    'qty' => $data['qty'],
                    'product_id' => $data['product']->id,
                    'channel_id' => $data['shipment']->order->channel->id
                ]);
        }

        $sellerInventory = $data['product']->inventories()
                ->where('vendor_id', $data['vendor_id'])
                ->where('inventory_source_id', $data['shipment']->inventory_source_id)
                ->first();


        if (!$sellerInventory)
            return;

        if (($qty = $sellerInventory->qty - $data['qty']) < 0) {
            $qty = 0;
        }

        $sellerInventory->update([
                'qty' => $qty
            ]);

        $adminInventory = $data['product']->inventories()
                ->where('vendor_id', 0)
                ->where('inventory_source_id', $data['shipment']->inventory_source_id)
                ->first();

        if ($adminInventory) {
            $adminInventory->update([
                    'qty' => $adminInventory->qty + $data['qty']
                ]);
        }
    }
}