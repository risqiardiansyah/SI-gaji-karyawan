<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListUserNotInvite extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return  [
            'users_code' => $this-> users_code,
            'name' => $this-> name,
            'email' => $this-> email,
            'profile' => ($this->profile == null ? '' : asset('storage/' . $this->profile)),
            'profile_ori' => $this->profile
        ];
    }
}