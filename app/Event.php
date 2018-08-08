<?php

namespace App;

use App\EventItem;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    //
    public function items()
    {
        // return $this->belongsToMany('App\Item')->using('App\EventItem');
        return $this->belongsToMany('App\Item','event_items')->withPivot('id','total_num','exchanged_num','winned_num','bargained_num','origin_price','bargain_price','bargain_min_times','bargain_max_times','bargain_min_price','bargain_max_price')->wherePivot('is_released',1);
    }
    public function getIsCurrentAttribute($value)
    {
        $now = time();
        $is_current = false;
        $start_time = strtotime($this->start_date);
        $end_time = strtotime($this->end_date.' 23:59:59');
        if( $now > $start_time && $now < $end_time ){
            $is_current = true;
        }
        return $is_current;
    }
    public function getTotalQtyAttribute($value)
    {
        $qty = EventItem::where('event_id', $this->id)->count();
        return $qty;
    }
}
