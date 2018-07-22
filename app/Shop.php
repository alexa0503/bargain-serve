<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    
    public function posted_items()
    {
        return $this->hasMany('App\Item')->where('is_posted',1)->orderBy('order_id','ASC');
    }
    public function items()
    {
        return $this->hasMany('App\Item');
    }
}
