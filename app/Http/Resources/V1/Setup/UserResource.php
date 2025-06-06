<?php

namespace App\Http\Resources\V1\Setup;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'note' => $this->note,
            'active' => $this->active,
            'created_by_id' => $this->created_by_id,
            'created_by_ip' => $this->created_by_ip,
            'created_by_agent' => $this->created_by_agent,
        ];
    }
}
