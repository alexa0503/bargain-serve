<?php

namespace App\Http\Resources\Administrator;

use Illuminate\Http\Resources\Json\JsonResource;

class Item extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'thumb' => asset($this->thumb),
            'image' => asset($this->image),
            'images' => $this->images,
            'descr' => $this->descr,
            'winned_num' => $this->winned_num,
            'bargained_num' => $this->bargained_num,
            'exchanged_num' => $this->exchanged_num,
            'order_id' => $this->order_id,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
