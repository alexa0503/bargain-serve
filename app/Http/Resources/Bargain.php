<?php

namespace App\Http\Resources;

use App\BargainUser;
use App\Http\Resources\Item as ItemResource;
use App\Http\Resources\Shop as ShopResource;
use App\Http\Resources\User as UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Route;

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
        $has_helped = false;
        $user = auth('api')->user();
        if( $user ){
            $bargain_user = BargainUser::where('user_id', $user->id)
                ->where('bargain_id', $this->id)
                ->first();
            if( $bargain_user ){
                $has_helped = true;
            }
        }
        $descr = str_replace(["\n\r","\n"],"<br/>",$this->item->descr);

        $item = [
            'id' => $this->event_item->id,
            'name' => $this->item->name,
            'image' => asset($this->item->image),
            'images' => $this->item->images,
            'descr' => $descr,
            'winned_num' => $this->event_item->winned_num,
            'exchanged_num' => $this->event_item->exchanged_num,
            'total_num' => $this->event_item->total_num,
            'bargained_num' => $this->event_item->bargained_num,
            'origin_price' => $this->event_item->origin_price,
            'bargain_price' => $this->event_item->bargain_price,
            'bargain_min_times' => $this->event_item->bargain_min_times,
            'bargain_max_times' => $this->event_item->bargain_max_times,
            'bargain_min_price' => $this->event_item->bargain_min_price,
            'bargain_max_price' => $this->event_item->bargain_max_price,
        ];
        return [
            'id' => $this->id,
            'shop_id' => $this->shop_id,
            $this->mergeWhen(Route::currentRouteName() != 'help', [
                'user' => new UserResource($this->user),
                // 'item' => new ItemResource($this->item),
                // 'shop' => new ShopResource($this->shop),
            ]),
            'item' => $item,
            'has_helped' => $has_helped,
            //'bargain_users' => UserResource::collection($this->bargain_users),
            'joined_times' => $this->joined_times,
            'current_price' => $this->current_price,
            'is_winned' => $this->is_winned,
            'has_bought' => $this->has_bought,
            'qr_code' => asset('codes/'.$this->id.'.png'),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
