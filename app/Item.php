<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Item extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    public $casts = [
        'images' => 'array'
    ];
    
    public function shop()
    {
        return $this->belongsTo('App\Shop');
    }

    public function events()
    {
        return $this->belongsToMany('App\Events','event_items');
    }
}
