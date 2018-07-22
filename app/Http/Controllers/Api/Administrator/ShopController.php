<?php

namespace App\Http\Controllers\Api\Administrator;

use Intervention\Image\Facades\Image as Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Administrator\Shop as ShopResource;
use App\Shop;
use Validator;
class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if( $request->keyword ){
            $model = Shop::where('name', 'LIKE', '%'.$request->keyword.'%')->paginate(20);
        }else{
            $model = Shop::paginate(20);
        }
        return ShopResource::collection($model);
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
        
        if( !file_exists(base_path('public/codes/shops/').$bargain->id.'.png') ){
            $mini_program = EasyWeChat::MiniProgram();
            $response = $mini_program->app_code->getUnlimit($bargain->id,[
                'page'=>'pages/bargain/index',
                'width'=>300,
                'auto_color'=>false,
                'is_hyaline'=>true,
                'line_color'=>(object)['r'=>0,'g'=>0,'b'=>0],
            ]);
            $response->saveAs(base_path('public/codes/shops/'), $bargain->id.'.png');
        }
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
        
        $messages = [
            'name.*' => '店铺名必须填写且不能超过100个字符~',
            'title.*' => '页面标题必须填写且不能超过100个字符~',
            'header_image.*' => '请上传头部图片~',
            'thumb.*' => '请上传店铺头像~',
            'tel.*' => '请输入联系方式~',
            'start_date.*' => '请选择活动开始日期~',
            'end_date.*' => '请选择活动结束日期~',
        ];
        $rules = [
            'name' => 'required|max:100',
            'title' => 'required|max:100',
            'header_image' => 'required',
            'thumb' => 'required',
            'tel' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ];
        $validator = Validator::make($request->all(), $rules,$messages);
        
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['ret'=>1001, 'errMsg'=>$errors]);
        }
        $pattern = '/^data:image\/(jpg|png|jpeg);base64,(.*)/i';
        if( preg_match($pattern, $request->input('thumb'), $matches) ){
            $filename = 'stores/header'.date('YmdHis').'.'.$matches[1];
            Storage::disk('public')->put($filename, \base64_decode($matches[2]));
            $image = Image::make(Storage::disk('public')->path($filename));
            if( $image->height() > $image->width() ){
                $image->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }else{
                $image->resize(null, 300, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }
            $image->save(Storage::disk('public')->path($filename));
            $thumb = Storage::disk('public')->url($filename);
        }
        else{
            $thumb = $request->input('thumb');
        }
        if( preg_match($pattern, $request->input('header_image'), $matches) ){
            $filename = 'stores/header'.date('YmdHis').'.'.$matches[1];
            Storage::disk('public')->put($filename, \base64_decode($matches[2]));
            $header_image = Storage::disk('public')->url($filename);

        }
        else{
            $header_image = $request->input('header_image');
        }
        $item = Shop::find($id);
        $item->name = $request->input('name');
        $item->title = $request->input('title');
        $item->tel = $request->input('tel');
        $item->header_image = $header_image;
        $item->start_date = $request->input('start_date');
        $item->end_date = $request->input('end_date');
        $item->address = $request->input('address');
        $item->thumb = $thumb;
        // $item->share_image = $share_image;
        // $item->share_descr = $request->input('share_descr');
        // $item->share_title = $request->input('share_title');
        $item->save();
        return response()->json(['ret'=>0]);
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
