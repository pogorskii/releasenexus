<?php

namespace App\Http\Resources;

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
        $user = $this->only([
            'id',
            'first_name',
            'last_name',
            'email',
            'email_verified_at',
            'created_at',
            'updated_at',
        ]);

        $user['full_name'] = $this->first_name.' '.$this->last_name;

        return $user;
    }
}
