<?php

namespace App\Http\Resources\Administrator;

use Illuminate\Http\Resources\Json\JsonResource;

class Event extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // $now = time();
        // $is_current = false;
        // $start_time = strtotime($this->start_date);
        // $end_time = strtotime($this->end_date.' 23:59:59');
        // if( $now > $start_time && $now < $end_time ){
        //     $is_current = true;
        // }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'shop_id' => $this->shop_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'total_qty' => $this->total_qty,
            'is_current' => $this->is_current,
        ];
        // return parent::toArray($request);
    }
}
