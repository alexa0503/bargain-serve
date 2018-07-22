<?php

namespace App\Http\Resources\Administrator;

use Illuminate\Http\Resources\Json\JsonResource;

class Bargain extends JsonResource
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
            'item_name' => $this->item->name,
            'user_name' => $this->user->nickname,
            'user_id' => $this->user_id,
            'joined_times' => $this->joined_times,
            'current_price' => $this->current_price,
            'is_winned' => $this->is_winned,
            'has_bought' => $this->has_bought,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
