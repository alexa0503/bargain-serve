<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Event;
class Shop extends Model
{
    
    public function released_items()
    {
        // 通过冗余值去判断当前店铺商品情况
        $limit_num = $this->limit_num;
        // return $this->hasMany('App\Item')->where('is_released',1)->limit($limit_num)->orderBy('order_id','ASC');
    }
    public function preview_items()
    {
        $limit_num = $this->limit_num;
        return $this->hasMany('App\Item')->limit($limit_num)->orderBy('order_id','ASC');
    }
    public function events()
    {
        return $this->hasMany('App\Event');
    }
    public static function currentEvent()
    {
        $date = date('Y-m-d');
        $event = Event::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();
        return $event;
        //return $this->hasMany('App\Event')->where()->first();
    }
}
