<?php

namespace App\Http\Resources\Administrator;

use Illuminate\Http\Resources\Json\JsonResource;

class Shop extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
            'title' => $this->title,
            'name' => $this->name,
            'header_image' => asset($this->header_image),
            'thumb' => asset($this->thumb),
            'kv_images' => $this->kv_images,
            'tel' => $this->tel,
            'descr' => $this->descr,
            'share_title' => $this->share_title,
            'share_desc' => $this->share_desc,
            'share_image' => asset($this->share_image),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'visit_times' => $this->visit_times,
            'address' => $this->address,
            'code_image' => asset('codes/shops/'.$this->id.'.png'),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
