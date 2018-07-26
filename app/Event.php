<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    //
    public function items()
    {
        return $this->belongsToMany('App\Item','event_item','item_id','event_id');
    }
}
