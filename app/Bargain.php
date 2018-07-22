<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bargain extends Model
{
    public function item()
    {
        return $this->belongsTo('App\Item');
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
