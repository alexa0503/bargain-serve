<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bargain extends Model
{
    public function event_item()
    {
        return $this->belongsTo('App\EventItem','event_item_id');
    }
    public function item()
    {
        return $this->belongsTo('App\Item','item_id');
    }
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function shop()
    {
        return $this->belongsTo('App\Shop');
    }
}
