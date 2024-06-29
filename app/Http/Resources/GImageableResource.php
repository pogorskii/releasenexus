<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GImageableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $imageable = parent::toArray($request);
        $imageable['image'] = $this->whenLoaded('image', fn() => (new GImageResource($this->image))->resolve());

        return $imageable;
    }
}
