<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Shop;
use App\Http\Resources\Shop as ShopResource;
use App\Http\Resources\ShopCollection;
//use Carbon\Carbon;

class ShopController extends Controller
{
    public function index()
    {
        $shops = Shop::limit(4)->get();
        return ShopResource::collection($shops)->additional([
            'meta' => [
                'descr' => '此小程序所有数据仅做演示用，无实际价值',
                'title' => 'Alix',
                'header_image' => asset('images/header_image.jpg'),
            ],
        ]);
        //return new ShopCollection($shops);
        //return response()->json($data);
    }
    public function view(Request $request, $id)
    {
        $shop = Shop::find($id);
        if( null == $shop ){
            return response()->json(['ret'=>1100,'errMsg'=>'不存在该店铺'],404);
        }
        # 前端判断
        /*
        $now = time();
        $start = strtotime($shop->start_date);
        $end = strtotime($shop->end_date.' 23:59:59');
        if( $now < $start ){
            return response()->json(['ret'=>1001,'errMsg'=>'活动未开始']);
        }
        elseif( $now > $end ){
            return response()->json(['ret'=>1002,'errMsg'=>'活动已结束']);
        }
        */
        $shop->visit_times += 1;
        $shop->save();
        return new ShopResource($shop);
    }
}
