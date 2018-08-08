<?php

namespace App\Http\Controllers\Api\Administrator;

use App\Http\Controllers\Controller;
use App\Http\Resources\Administrator\Item as ItemResource;
use App\Item;
use App\EventItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class ItemController extends Controller
{
    protected $can_add_qty = 20;
    protected $messages = [
        'name.*' => '店铺名必须填写且不能超过100个字符~',
        'image.*' => '请上传图片~',
        'descr.*' => '请填写描述~',
        // 'total_num.*' => '请填写商品总数~',
        // 'origin_price.*' => '请填写商品原价~',
        // 'bargain_price.*' => '请填写商品可砍最低价~',
    ];
    protected $rules = [
        'name' => 'required|max:100',
        'image' => 'required',
        'descr' => 'required',
        // 'total_num' => 'required',
        // 'origin_price' => 'required',
        // 'bargain_price' => 'required',
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $admin = auth('admin')->user();
        $model = Item::orderBy('order_id', 'ASC')->orderBy('created_at', 'DESC');
        if ($admin->shop_id) {
            $model->where('shop_id', $admin->shop_id);
        }
        if( $request->input('event_id') ){
            $item_ids = EventItem::where('event_id', $request->input('event_id'))->where('shop_id', $admin->shop_id)->get()->map(function($item){
                return $item->item_id;
            });
            $model->whereNotIn('id', $item_ids);
            $items = $model->get();
        }
        else{
            $items = $model->paginate(20);
        }
        return ItemResource::collection($items);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages = $this->messages;
        $rules = $this->rules;
        $validator = Validator::make($request->all(), $rules, $messages);

        $count = Item::where('shop_id', $shop_id)->count();
        $can_add_qty = $this->can_add_qty;
        $validator->after(function ($validator) use($count,$can_add_qty) {
            if($count >= $can_add_qty){
                $validator->errors()->add('name', '抱歉，最多只能添加'.$can_add_qty.'个商品。');
            }
        });
        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }
        $item = new Item;

        $pattern = '/^data:image\/(jpg|png|jpeg);base64,(.*)/i';
        if (preg_match($pattern, $request->input('image'), $matches)) {
            $filename = 'items/' . date('YmdHis') . '.' . $matches[1];
            Storage::disk('public')->put($filename, \base64_decode($matches[2]));
            $image = Storage::disk('public')->url($filename);

        } else {
            $image = $request->input('image');
        }
        
        if( !$request->input('shop_id') ){
            $admin = auth('admin')->user();
            $shop_id = $admin->shop_id;
        }
        else{
            $shop_id = $request->input('shop_id');
        }
        $images = [];
        foreach($request->input('images') as $img ){
            if (preg_match($pattern, $img, $matches)) {
                $filename = 'items/' . date('YmdHis') . str_random(6) . '.' . $matches[1];
                Storage::disk('public')->put($filename, \base64_decode($matches[2]));
                $images[] = Storage::disk('public')->url($filename);
            }
            else{
                $images[] = asset($img);
            }
        }
        $item->name = $request->input('name');
        $item->image = $image;
        $item->images = $images;
        $item->shop_id = $shop_id;
        $item->descr = $request->input('descr');
        $item->is_released = 0;
        // $item->total_num = $request->input('total_num');
        // $item->origin_price = $request->input('origin_price');
        // $item->bargain_price = $request->input('bargain_price');
        // $item->bargain_min_price = $request->input('bargain_min_price');
        // $item->bargain_max_price = $request->input('bargain_max_price');
        // $item->bargain_min_times = $request->input('bargain_min_times');
        // $item->bargain_max_times = $request->input('bargain_max_times');
        // $item->is_posted = 0;
        // $item->exchange_password = str_random(6);
        $count = Item::where('shop_id', $shop_id)->count();
        $item->order_id = $count + 1;
        $item->save();
        return response()->json(['ret' => 0]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $item = Item::find($id);
        return new ItemResource($item);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // 仅更新排序
        if ($request->input('type') == 'order') {
            $item = Item::find($id);
            $item->order_id = $request->input('order_id');
            $item->save();
            return response()->json(['ret' => 0]);
        } else {
            $messages = $this->messages;
            $rules = $this->rules;
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json($validator->errors(),422);
            }
            $item = Item::find($id);

            $pattern = '/^data:image\/(jpg|png|jpeg);base64,(.*)/i';
            if (preg_match($pattern, $request->input('image'), $matches)) {
                $filename = 'items/' . date('YmdHis') . '.' . $matches[1];
                Storage::disk('public')->put($filename, \base64_decode($matches[2]));
                $image = Storage::disk('public')->url($filename);

            } else {
                $image = $request->input('image');
            }

            $images = [];
            foreach($request->input('images') as $img ){
                if (preg_match($pattern, $img, $matches)) {
                    $filename = 'items/' . date('YmdHis') . str_random(6) . '.' . $matches[1];
                    Storage::disk('public')->put($filename, \base64_decode($matches[2]));
                    $images[] = Storage::disk('public')->url($filename);
                }
                else{
                    $images[] = asset($img);
                }
            }
            $item->image = $image;
            $item->images = $images;
            $item->name = $request->input('name');
            $item->descr = $request->input('descr');
            // $item->total_num = $request->input('total_num');
            // $item->bargain_price = $request->input('bargain_price');
            // $item->origin_price = $request->input('origin_price');
            // $item->bargain_min_price = $request->input('bargain_min_price');
            // $item->bargain_max_price = $request->input('bargain_max_price');
            // $item->bargain_min_times = $request->input('bargain_min_times');
            // $item->bargain_max_times = $request->input('bargain_max_times');
            $item->save();
            return response()->json(['ret' => 0]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = Item::find($id);
        $count = EventItem::where('item_id', $id)->count();
        if (!$item) {
            return response()->json(['ret' => 1001, 'errMsg' => '不存在该商品'], 404);
        } elseif( $count > 1) {
            return response()->json(['ret' => 1002, 'errMsg' => '发布中的商品无法删除'], 422);
        }
        else {
            $item->delete();
            return response()->json(['ret' => 0]);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function publish(Request $request, $id)
    {
        $item = Item::find($id);
        $published_num = Item::where('is_posted',1)
            ->where('shop_id', $item->shop_id)
            ->count();
        if (!$item) {
            return response()->json(['ret' => 1001, 'errMsg' => '不存在该商品'], 404);
        }elseif($published_num >= $item->shop->max_items_num){
            return response()->json(['ret' => 1002, 'errMsg' => '您已经无法发布更多商品了，请联系客服人员。']);
        } else {
            $item->is_posted = $request->input('type') == 'on' ? 1 : 0;
            $item->save();
            return response()->json(['ret' => 0]);
        }
    }
}
