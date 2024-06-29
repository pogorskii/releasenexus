<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GReleaseDateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $releaseDate = $this->only([
            'id',
            'origin_id',
            'category',
            //            'checksum',
            //            'created_at',
            //            'date',
            'human',
            //            'm',
            //            'region',
            'status_id',
            //            'updated_at',
            //            'y',
            'dateable_id',
            //            'dateable_type',
        ]);

//        if dateable_type is App\Models\Game, then include the game resource
        $releaseDate['dateable'] = $this->whenLoaded('dateable', function () {
            return new GameResource($this->dateable);
        });

//        $releaseDate['game'] = $this->whenLoaded('game', function () {
//            return new GameResource($this->game);
//        });

        return $releaseDate;
    }
}
