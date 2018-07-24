<?php

namespace App\Http\Resources;

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
        $disabled = $this->winned_num >= $this->total_num;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => asset($this->image),
            'images' => $this->images,
            'descr' => $this->descr,
            'total_num' => $this->total_num,
            'winned_num' => $this->winned_num,
            'bargained_num' => $this->bargained_num,
            'origin_price' => $this->origin_price,
            'bargain_price' => $this->bargain_price,
            'disabled' => $disabled
        ];
    }
}
