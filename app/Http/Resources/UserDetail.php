<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserDetail extends JsonResource
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
            'id' => $this->id,
            'users_code' => $this->users_code,
            'name' => $this->name,
            'email' => $this->email,
            'address_user' => $this->address_user,
            'company_user' => $this->company_user,
            'province_user' => $this->province_user,
            'city_user' => $this->city_user,
            'role' => $this->role,
            'profile' => ($this->profile == null ? '' : asset('storage/' . $this->profile)),
            'profile_ori' => $this->profile
        ];
    }
}
