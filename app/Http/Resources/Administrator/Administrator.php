<?php

namespace App\Http\Resources\Administrator;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Administrator\Shop as ShopResource;

class Administrator extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_super' => $this->shop_id ? false : true,
            'email' => $this->email,
            'shop' => new ShopResource($this->shop),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
        //return parent::toArray($request);
    }
}
