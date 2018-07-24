<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Item as ItemResource;
use Route;
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
        $now = time();
        $start = strtotime($this->start_date);
        $end = strtotime($this->end_date.' 23:59:59');
        $state = 0;
        if( $now < $start ){
            $state = 1;
        }
        elseif( $now > $end ){
            $state = 2;
        }
        //dd(\Route::currentRouteName());
        if( $request->preview == 'y' ){
            $items = ItemResource::collection($this->preview_items);
        }
        else{
            $items = ItemResource::collection($this->posted_items);
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'title' => $this->title,
            'tel' => $this->tel,
            'header_image' => asset($this->header_image),
            'thumb' => asset($this->thumb),
            'share_image' => $this->share_image,
            'share_title' => $this->share_title,
            'share_descr' => $this->share_descr,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'address' => $this->address,
            'descr' => $this->descr,
            'visit_times' => $this->visit_times,
            $this->mergeWhen(Route::currentRouteName() != 'bargain',[
                'items' => $items,
            ]),
            'state' => $state, // 店铺当前状态
            //'created_at' => $this->created_at,
            //'updated_at' => $this->updated_at,
        ];
    }
}
