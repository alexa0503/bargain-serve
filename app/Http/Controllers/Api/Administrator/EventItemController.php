<?php

namespace App\Http\Controllers\Api\Administrator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\EventItem;
use App\Event;
use App\Http\Resources\Administrator\EventItem as EventItemResource;
use Validator;

class EventItemController extends Controller
{
    protected $release_rules = [
        'is_released' => 'boolean',
    ];
    protected $rules = [
        'origin_price' => ['required', 'regex:/^\d{1,8}(\.\d{1,2})?$/'],
        'bargain_price' => 'regex:/^\d{1,8}(\.\d{1,2})?$/',
        'total_num' => 'regex:/^[1-9]+\d{0,5}$/',
        'bargain_min_price' => 'regex:/^\d{1,8}(\.\d{1,2})?$/',
        'bargain_max_price' => 'regex:/^\d{1,8}(\.\d{1,2})?$/',
        'bargain_min_times' => 'regex:/^[1-9]+\d{0,5}$/',
        'bargain_max_times' => 'regex:/^[1-9]+\d{0,5}$/',
    ];
    protected $messages = [
        'item_id.*' => '请选择活动商品',
        'is_released.*' => '',
        'origin_price.*' => '原价必须是2位小数',
        'bargain_price.*' => '价格必须是2位小数',
        'total_num.*' => '总数必须是大于0的整数',
        'bargain_min_price.*' => '价格必须是2位小数',
        'bargain_max_price.*' => '价格必须是2位小数',
        'bargain_min_times.*' => '必须是整数',
        'bargain_max_times.*' => '必须是整数',
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $admin = auth('admin')->user();

        $model = EventItem::with(['item'=>function($query){
            $query->orderBy('order_id', 'ASC');
        }])->where('event_id',$id);
        if ($admin->shop_id) {
            $model->where('shop_id', $admin->shop_id);
        }
        $items = $model->paginate(20);
        return EventItemResource::collection($items);
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
    public function store(Request $request, $event_id)
    {
        $messages = $this->messages;
        $rules = $this->rules;
        $rules['item_id'] = [
            'required',
            'exists:items,id'
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        $admin = auth('admin')->user();
        $event = Event::find($event_id);
        $count = EventItem::where('event_id', $event_id)->count();
        $has_permission = $event && $event->shop_id == $admin->shop_id;
        $max_num = 50;
        $validator->after(function ($validator) use($count, $max_num, $has_permission) {
            if( $count > $max_num ){
                $validator->errors()->add('error','最多不能超过'.$max_num.'商品。');
            }
            if( !$has_permission ){
                $validator->errors()->add('error','抱歉，您没有权限。');
            }
        });
        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }
        $event_item = new EventItem;
        $event_item->item_id = $request->item_id;
        $event_item->event_id = $event_id;
        $event_item->shop_id = $event->shop_id;
        $event_item->origin_price = $request->origin_price;
        $event_item->bargain_price = $request->bargain_price;
        $event_item->bargain_min_times = $request->bargain_min_times;
        $event_item->bargain_max_times = $request->bargain_max_times;
        $event_item->bargain_min_price = $request->bargain_min_price;
        $event_item->bargain_max_price = $request->bargain_max_price;
        $event_item->total_num = $request->total_num;
        $event_item->is_released = 0;
        $event_item->save();
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $event_id, $id)
    {
        $messages = $this->messages;
        $rules = $request->input('type') == 'release' ? $this->release_rules : $this->rules;
        $validator = Validator::make($request->all(), $rules, $messages);

        $event_item = EventItem::find($id);
        $admin = auth('admin')->user();
        $validator->after(function ($validator) use($event_item,$admin,$request) {
            if( $event_item->shop_id != $admin->shop_id ){
                $validator->errors()->add('error','抱歉，您没有权限修改此商品。');
            }
            // 活动中的商品无法修改
            if( $event_item->event->is_current && $event_item->is_released == 1 ){
                if( $request->input('type') == 'release'){
                    $validator->errors()->add('error','抱歉，活动中的商品无法下架。');
                }
                else{
                    $validator->errors()->add('error','抱歉，活动中的商品无法修改。');
                }
            }
        });
        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }
        if( $request->input('type') == 'release' ){
            $event_item->is_released = $request->input('is_released') == 1 ? 1 : 0;
        }
        else{
            $event_item->origin_price = $request->origin_price;
            $event_item->bargain_price = $request->bargain_price;
            $event_item->bargain_min_times = $request->bargain_min_times;
            $event_item->bargain_max_times = $request->bargain_max_times;
            $event_item->bargain_min_price = $request->bargain_min_price;
            $event_item->bargain_max_price = $request->bargain_max_price;
            $event_item->total_num = $request->total_num;
        }
        $event_item->save();
        return response()->json(['ret' => 0]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
