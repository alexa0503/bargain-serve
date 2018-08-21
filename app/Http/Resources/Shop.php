<?php

namespace App\Http\Resources;

use App\Shop as S;
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
        // 
        $descr = str_replace(["\n\r","\n"],"<br/>",$this->descr);
        $current_event = S::currentEvent();
        if($current_event){

            $items = $current_event->items->map(function($item){
                $descr = str_replace(["\n\r","\n"],"<br/>",$this->descr);
                return [
                    'id' => $item->pivot->id,
                    'name' => $item->name,
                    'image' => asset($item->image),
                    'images' => $item->images,
                    'descr' => $descr,
                    'winned_num' => $item->pivot->winned_num,
                    'exchanged_num' => $item->pivot->exchanged_num,
                    'total_num' => $item->pivot->total_num,
                    'bargained_num' => $item->pivot->bargained_num,
                    'origin_price' => $item->pivot->origin_price,
                    'bargain_price' => $item->pivot->bargain_price,
                    'bargain_min_times' => $item->pivot->bargain_min_times,
                    'bargain_max_times' => $item->pivot->bargain_max_times,
                    'bargain_min_price' => $item->pivot->bargain_min_price,
                    'bargain_max_price' => $item->pivot->bargain_max_price,
                ];
            });
        }
        else{
            $items = [];
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
            'descr' => $descr,
            'visit_times' => $this->visit_times,
            'items' => $items,
            // 'items' => $items,
            // $this->mergeWhen(Route::currentRouteName() != 'bargain',[
            //     'items' => $items,
            // ]),
            // 'state' => $state, // 店铺当前状态
            //'created_at' => $this->created_at,
            //'updated_at' => $this->updated_at,
        ];
    }
}
