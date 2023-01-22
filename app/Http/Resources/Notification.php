<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Notification extends JsonResource
{
    public function toArray($request)
    {
        return  [
            'timestamp' => $this->created_at,
            'text' => $this->message,
            'path' => $this->link,
            'type' => $this->type,
            'id' => $this->id,
            'status' => $this->status
        ];
    }
}