<?php

namespace App\Http\Controllers\Api;

use App\Bargain;
use App\BargainUser;
use App\Http\Controllers\Controller;
use App\Http\Resources\Bargain as BargainResource;
use App\Http\Resources\BargainUser as BargainUserResource;
use App\Item;
use App\Shop;
use DB;
use Illuminate\Http\Request;
use Overtrue\LaravelWeChat\Facade as EasyWeChat;


class BargainController extends Controller
{
    // 查看砍价商品信息
    public function view(Request $request, $id)
    {
        $bargain = Bargain::find($id);
        if (null == $bargain) {
            return response()->json(['ret' => 1100, 'errMsg' => '无此记录'], 404);
        }

        # 核实店铺状态
        $shop = $bargain->shop;
        $start_time = strtotime($shop->start_date);
        $end_time = strtotime($shop->end_date . ' 23:59:59');
        $now = time();
        if ($now < $start_time) {
            return response()->json(['ret' => 1002, 'errMsg' => '活动未开始'], 422);
        } elseif ($now > $end_time) {
            return response()->json(['ret' => 1003, 'errMsg' => '活动已结束'], 422);
        }
        return new BargainResource($bargain);
    }
    // 帮忙砍价用户信息
    public function bargainUsers(Request $request, $bargain_id)
    {
        $bargain_users = BargainUser::where('bargain_id', $bargain_id)->get();
        return BargainUserResource::collection($bargain_users);
    }
    // 用户帮忙/参与砍价
    public function help(Request $request, $id)
    {
        $bargain = Bargain::find($id);
        $user = auth('api')->user();
        if (null == $bargain) {
            return response()->json(['ret' => 1100, 'errMsg' => '无此记录'], 404);
        }

        # 核实店铺状态
        $shop = $bargain->shop;
        $start_time = strtotime($shop->start_date);
        $end_time = strtotime($shop->end_date . ' 23:59:59');
        $now = time();
        if ($now < $start_time) {
            return response()->json(['ret' => 1002, 'errMsg' => '活动未开始'], 422);
        } elseif ($now > $end_time) {
            return response()->json(['ret' => 1003, 'errMsg' => '活动已结束'], 422);
        }

        if ($bargain->is_winned == 1) {
            return response()->json(['ret' => 1001, 'errMsg' => '此商品已经砍到啦，无法继续']);
        }

        # 砍价事务处理
        $return = ['ret' => 0, 'errMsg' => ''];
        DB::beginTransaction();
        try {
            $bargain = Bargain::find($id);
            $item = Item::find($bargain->item_id);
            $bargain_user = BargainUser::where('user_id', $user->id)
                ->where('bargain_id', $bargain->id)
                ->select('id')
                ->first();

            if ($bargain_user) {
                $return = ['ret' => 1002, 'errMsg' => '你已经帮忙砍过啦'];
            } elseif ($bargain->is_winned == 1) {
                $return = ['ret' => 1003, 'errMsg' => '晚了一步，该砍价已完成啦'];
            } elseif ($item->winned_num >= $item->total_num) {
                $return = ['ret' => 1004, 'errMsg' => '晚了一步，该商品已被砍完啦'];
            } else {
                // 差价
                $spread_price = $item->origin_price - $item->bargain_price;
                if (empty($item->bargain_rules)) {
                    $min_price = ($spread_price) / 20;
                    $max_price = ($spread_price) / 10;
                    $rules = [
                        'min_price' => $min_price,
                        'max_price' => $max_price,
                        'min_times' => 10,
                        'max_times' => 20,
                    ];
                } else {
                    $rules = $item->bargain_rules;
                }
                # 如果没有达到最小次数 限定每次价格
                if ($rules['min_times'] > $bargain->join_times) {
                    $_price = $spread_price / ($rules['min_times'] - $bargain->join_times);
                    if ($rules['max_price'] > $_price && $rules['min_price'] < $_price) {
                        $rules['max_price'] = $_price;
                    }
                }

                $rand = rand(ceil($rules['min_price'] * 10000), ceil($rules['max_price'] * 10000));
                $bargain_price = $rand / 10000;
                // 根据砍价最大最小次数去调整
                // 仅剩一次机会
                if ($rules['max_times'] - $bargain->join_times <= 1) {
                    $bargain_price = $bargain->current_price - $item->bargain_price;
                }
                //$times = $rules['min_times'] - $rules['max_times'];
                // 砍出最低价
                elseif ($bargain->current_price - $bargain_price < $rules['min_price']) {
                    if ($bargain->joined_times < $rules['min_times']) {
                        $bargain_price = $rules['min_price'];
                    } else {
                        $bargain_price = $bargain->current_price - $rules['min_price'];
                        $bargain->is_winned = 1;
                        // 发送模板消息通知用户
                        if( $bargain->form_id ){
                            $end_date = date('y年m月d日', strtotime($shop->end_date));
                            $mini_program = EasyWeChat::MiniProgram();
                            $template_id = env('WECHAT_TEMPLATE_ID');
                            $res = $mini_program->template_message->send([
                                'touser' => $bargain->user->openid,
                                'template_id' => $template_id,
                                'page' => 'pages/bargains/index?scene='.$bargain->id,
                                'form_id' => $bargain->form_id,
                                'data' => [
                                    'keyword1' => $item->name,
                                    'keyword2' => $item->origin_price,
                                    'keyword3' => $item->bargain_price,
                                    'keyword4' => '砍价完成啦',
                                    'keyword5' => $end_date.'之前去领取哦',
                                ],
                            ]);
                            // dd($res);
                        }
                    }
                }
                $bargain_price = round($bargain_price, 2);
                $bargain->current_price -= $bargain_price;
                $bargain->joined_times += 1;
                $bargain->save();
                $new_bargain_user = new BargainUser;
                $new_bargain_user->user_id = $user->id;
                $new_bargain_user->bargain_id = $bargain->id;
                $new_bargain_user->item_id = $item->id;
                $new_bargain_user->bargain_price = $bargain_price;
                $new_bargain_user->save();
                $return = [
                    'ret' => 0,
                    'bargain' => new BargainResource($bargain),
                ];
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['ret' => 1200, 'errMsg' => $e->getMessage()], 422);
        }
        return response()->json($return);
    }
    // 生成砍价信息
    public function create(Request $request, $id)
    {
        $item = Item::find($id);
        if (null == $item || $item->is_posted == 0) {
            return response()->json(['ret' => 1100, 'errMsg' => '无此商品'], 404);
        }
        # 核实店铺状态
        $shop = $item->shop;
        $start_time = strtotime($shop->start_date);
        $end_time = strtotime($shop->end_date . ' 23:59:59');
        $now = time();
        if ($now < $start_time) {
            return response()->json(['ret' => 1002, 'errMsg' => '活动未开始'], 422);
        } elseif ($now > $end_time) {
            return response()->json(['ret' => 1003, 'errMsg' => '活动已结束'], 422);
        }

        $user = auth('api')->user();
        $bargain = Bargain::where('user_id', $user->id)->where('item_id', $item->id)->first();
        if (null == $bargain) {
            $bargain = new Bargain;
            $bargain->user_id = $user->id;
            $bargain->item_id = $item->id;
            $bargain->joined_times = 0;
            $bargain->current_price = $item->origin_price;
            $bargain->is_winned = 0;
            $bargain->has_bought = 0;
            $bargain->shop_id = $item->shop_id;
            $bargain->form_id = $request->input('form_id');
            $bargain->save();
        }
        if( !file_exists(base_path('public/codes/').$bargain->id.'.png') ){
            $mini_program = EasyWeChat::MiniProgram();
            $response = $mini_program->app_code->getUnlimit($bargain->id,[
                'page'=>'pages/exchange/index',
                'width'=>300,
                'auto_color'=>false,
                'is_hyaline'=>true,
                'line_color'=>(object)['r'=>0,'g'=>0,'b'=>0],
            ]);
            $response->saveAs(base_path('public/codes/'), $bargain->id.'.png');
        }
        return response()->json(['ret' => 0,'id'=>$bargain->id]);
    }
    // 用户所有得砍价信息
    public function index(Request $request)
    {
        $user = auth('api')->user();
        $bargains = Bargain::where('user_id', $user->id)->get();
        return BargainResource::collection($bargains);
    }

    // 砍价兑换
    public function exchange(Request $request, $id)
    {
        $bargain = Bargain::find($id);
        $password = $request->input('password');
        if( $bargain->is_winned != 1 ){
            return response()->json(['ret'=> 1002, 'errMsg'=>'没有完成砍价，无法兑换']);
        }
        elseif( $bargain->has_bought == 1 ){
            return response()->json(['ret'=> 1003, 'errMsg'=>'已经兑换过啦']);
        }
        elseif( $password == $bargain->password ){
            $bargain->has_bought = 1;
            $bargain->exchanged_at = date('Y-m-d H:i:s');
            $bargain->save();
            return response()->json(['ret'=> 0, 'errMsg'=>'']);
        }else{
            return response()->json(['ret'=> 1001, 'errMsg'=>'密码错误']);
        }
    }
}
