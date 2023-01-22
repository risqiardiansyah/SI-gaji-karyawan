<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SignerList extends JsonResource
{
    public function toArray($request)
    {
        return  [
            'label' => $this->name.' - '.$this->position,
            'value' => $this->signer_code
        ];
    }
}