<?php

namespace App\Http\Controllers\Api\Administrator;

use App\Event;
use App\Http\Controllers\Controller;
use App\Http\Resources\Administrator\Event as EventResource;
use Illuminate\Http\Request;
use Validator;

class EventController extends Controller
{
    protected $rules = [
        'name' => 'required',
        'start_date' => 'required|date',
        'end_date' => 'required|date',
    ];
    protected $messages = [
        'name.*' => '请输入活动名称',
        'start_date.*' => '开始日期格式不正确',
        'end_date.*' => '截止日期格式不正确',
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $admin = auth('admin')->user();
        $model = Event::where('shop_id', $admin->shop_id);
        if ($request->input('page')) {
            if ($request->input('keyword')) {
                $model->where('name', 'LIKE', '%' . $request->keyword . '%');
            }
            $events = $model->paginate(20);
        } else {
            $events = $model->get();
        }

        return EventResource::collection($events);
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
        $admin = auth('admin')->user();
        $shop_id = $admin->shop_id;
        $messages = $this->messages;
        $rules = $this->rules;
        $validator = Validator::make($request->all(), $rules, $messages);

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        // 开始日期是否与其他活动冲突
        if ($start_date && $end_date) {
            $event1 = Event::where('start_date', '<=', $start_date)
                ->select('id')
                ->where('end_date', '>=', $start_date)
                ->where('shop_id', $shop_id)
                ->first();
            $event2 = Event::where('start_date', '<=', $end_date)
                ->select('id')
                ->where('end_date', '>=', $end_date)
                ->where('shop_id', $shop_id)
                ->first();
            $validator->after(function ($validator) use ($start_date, $end_date, $shop_id, $event1, $event2) {
                if (strtotime($start_date) > strtotime($end_date)) {
                    $validator->errors()->add('start_date', '开始日期不能晚于结束日期');
                } elseif (strtotime($end_date) - strtotime($start_date) > 31 * 24 * 3600) {
                    $validator->errors()->add('start_date', '活动周期不能超过一个月');
                } elseif (strtotime($end_date) - strtotime($start_date) < 3 * 24 * 3600) {
                    $validator->errors()->add('start_date', '活动周期不能小于3天');
                } elseif ($event1) {
                    $validator->errors()->add('start_date', '开始日期与其他活动冲突');
                } elseif ($event2) {
                    $validator->errors()->add('end_date', '结束日期与其他活动冲突');
                }
            });
        }

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $event = new Event;
        $event->start_date = $request->start_date;
        $event->name = $request->name;
        $event->end_date = $request->end_date;
        $event->shop_id = $shop_id;
        $event->save();
        return response()->json([]);

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
    public function update(Request $request, $id)
    {
        $admin = auth('admin')->user();
        $shop_id = $admin->shop_id;
        $messages = $this->messages;
        $rules = $this->rules;
        $validator = Validator::make($request->all(), $rules, $messages);
        $event = Event::find($id);

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        // 开始日期是否与其他活动冲突
        $event1 = Event::where('start_date', '<=', $start_date)
            ->select('id')
            ->where('end_date', '>=', $start_date)
            ->where('shop_id', $shop_id)
            ->where('id', '!=', $id)
            ->first();
        $event2 = Event::where('start_date', '<=', $end_date)
            ->select('id')
            ->where('end_date', '>=', $end_date)
            ->where('shop_id', $shop_id)
            ->where('id', '!=', $id)
            ->first();
        $validator->after(function ($validator) use ($start_date, $end_date, $shop_id, $event1, $event2) {
            // if( strtotime($event->end_date) < time() ){
            //     $validator->errors()->add('start_date', '开始日期不能晚于结束日期');
            // }
            // else
            if (strtotime($start_date) > strtotime($end_date)) {
                $validator->errors()->add('start_date', '开始日期不能晚于结束日期');
            } elseif (strtotime($end_date) - strtotime($start_date) > 31 * 24 * 3600) {
                $validator->errors()->add('start_date', '活动周期不能超过一个月');
            } elseif (strtotime($end_date) - strtotime($start_date) < 3 * 24 * 3600) {
                $validator->errors()->add('start_date', '活动周期不能小于3天');
            } elseif ($event1) {
                $validator->errors()->add('start_date', '开始日期与其他活动冲突');
            } elseif ($event2) {
                $validator->errors()->add('end_date', '结束日期与其他活动冲突');
            }
        });
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $event->start_date = $request->start_date;
        $event->name = $request->name;
        $event->end_date = $request->end_date;
        $event->save();
        return response()->json([]);
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
