<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    
    public function posted_items()
    {
        return $this->hasMany('App\Item')->where('is_posted',1)->limit(10)->orderBy('order_id','ASC');
    }
    public function preview_items()
    {
        return $this->hasMany('App\Item')->limit(10)->orderBy('order_id','ASC');
    }
    public function items()
    {
        return $this->hasMany('App\Item')->orderBy('order_id','ASC');
    }
}
