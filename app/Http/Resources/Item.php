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
        // 判断商品是否可砍价
        $disabled = false;
        $descr = str_replace(["\n\r","\n"],"<br/>",$this->descr);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => asset($this->image),
            'images' => $this->images,
            'descr' => $descr,
            'winned_num' => $this->winned_num,
            // 'bargained_num' => $this->bargained_num,
            // 'exchanged_num' => $this->exchanged_num,
            // 'disabled' => $disabled
        ];
    }
}
