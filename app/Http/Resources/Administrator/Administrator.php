<?php

namespace App\Http\Resources\Administrator;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Administrator\Shop as ShopResource;
use Overtrue\LaravelWeChat\Facade as EasyWeChat;

class Administrator extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if( file_exists(base_path('public/codes/shops/').$this->shop_id.'.png') ){
            $mini_program = EasyWeChat::MiniProgram();
            $response = $mini_program->app_code->getUnlimit($this->shop_id,[
                'page'=>'pages/index/index',
                'width'=>800,
                'auto_color'=>false,
                'is_hyaline'=>true,
                'line_color'=>(object)['r'=>0,'g'=>0,'b'=>0],
            ]);
            $response->saveAs(base_path('public/codes/shops/'), $this->shop_id.'.png');
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_super' => $this->shop_id ? false : true,
            'is_activated' => $this->is_activated,
            'email' => $this->email,
            'shop' => new ShopResource($this->shop),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
        //return parent::toArray($request);
    }
}
