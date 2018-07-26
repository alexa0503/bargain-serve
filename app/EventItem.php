<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventItem extends Model
{
    
    public function shop()
    {
        return $this->belongsTo('App\Shop');
    }
    public function event()
    {
        return $this->belongsTo('App\Event');
    }
    public function item()
    {
        return $this->belongsTo('App\Item');
    }
}
