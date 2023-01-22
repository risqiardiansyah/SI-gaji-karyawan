<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListUserInvite extends JsonResource
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
            'invitation_code' => $this-> invitation_code,
            'name' => $this-> name,
            'email' => $this-> email,
            'status' => $this-> status,
            'id_inv' => $this-> id_inv,
            'profile' => ($this->profile == null ? '' : asset('storage/' . $this->profile)),
            'profile_ori' => $this->profile
        ];
    }
}