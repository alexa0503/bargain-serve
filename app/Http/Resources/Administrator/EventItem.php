<?php

namespace App\Http\Resources\Administrator;

use Illuminate\Http\Resources\Json\JsonResource;

class EventItem extends JsonResource
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
            'name' => $this->item->name,
            'event_name' => $this->event->name,
            'event_id' => $this->event->id,
            'total_num' => $this->total_num,
            'winned_num' => $this->winned_num,
            'bargained_num' => $this->bargained_num,
            'exchanged_num' => $this->exchanged_num,
            'origin_price' => $this->origin_price,
            'bargain_price' => $this->bargain_price,
            'is_released' => $this->is_released,
            'bargain_min_price' => $this->bargain_min_price,
            'bargain_max_price' => $this->bargain_max_price,
            'bargain_min_times' => $this->bargain_min_times,
            'bargain_max_times' => $this->bargain_max_times,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
