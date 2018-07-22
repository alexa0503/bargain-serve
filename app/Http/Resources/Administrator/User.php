<?php

namespace App\Http\Resources\Administrator;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'nickname' => $this->nickname,
            'country' => $this->country,
            'city' => $this->city,
            'province' => $this->province,
            'sex' => $this->sex == 0 ? '男' : '女',
            'tel' => $this->tel,
            'avatar' => asset($this->avatar),
            'openid' => $this->openid,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
