<?php


namespace Webkul\API\Http\Resources\Marketplace;


use Illuminate\Http\Resources\Json\JsonResource;

class Seller extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'shop_title' => $this->shop_title,
            'description' => $this->description,
            'logo' => $this->logo,
            'address1' => $this->address1,
            'address2' => $this->address2,
            'phone' => $this->phone,
            'state' => $this->state,
            'city' => $this->city,
            'country' => $this->country,
            'postcode' => $this->postcode,
            'return_policy' => $this->return_policy,
            'shipping_policy' => $this->shipping_policy,
            'privacy_policy' => $this->privacy_policy,
        ];
    }
}