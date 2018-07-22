<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User as UserResource;

class BargainUser extends JsonResource
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
            'bargain_price' => $this->bargain_price,
            'user' => new UserResource($this->user),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
