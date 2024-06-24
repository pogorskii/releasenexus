<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $game = $this->only([
            'id',
            'name',
            'slug',
            //            'url',
            //            'created_at',
            //            'updated_at',
            'first_release_date',
            //            'aggregated_rating',
            //            'aggregated_rating_count',
            //            'alternative_names',
            'category',
            //            'checksum',
            //            'hypes',
            //            'rating',
            //            'rating_count',
            //            'status',
            //            'storyline',
            //            'summary',
            //            'tags',
            //            'total_rating',
            //            'total_rating_count',
            //            'version_title',
            //            'synced_at',
        ]);

//        $game['release_dates'] = $this->whenLoaded('releaseDates', function () {
//            return $this->releaseDates->map(function ($releaseDate) {
//                return [
//                    'id'            => $releaseDate->id,
//                    'category'      => $releaseDate->category,
//                    'checksum'      => $releaseDate->checksum,
//                    'created_at'    => $releaseDate->created_at,
//                    'date'          => $releaseDate->date,
//                    'human'         => $releaseDate->human,
//                    'm'             => $releaseDate->m,
//                    'region'        => $releaseDate->region,
//                    'status_id'     => $releaseDate->status_id,
//                    'updated_at'    => $releaseDate->updated_at,
//                    'y'             => $releaseDate->y,
//                    'dateable_id'   => $releaseDate->dateable_id,
//                    'dateable_type' => $releaseDate->dateable_type,
//                ];
//            });
//        });

        $game['covers'] = $this->whenLoaded('covers', fn () => GImageableResource::collection($this->covers)->resolve());
//            return $this->covers;
//            return $this->covers->map(function ($cover) {
//                return [
//                    'id'    => $cover->id,
//                    'cover' => $cover,
//                ];
//            });
//            foreach ($this->covers as $cover) {
//                return [
//                    'id'            => $cover->id,
//                    'alpha_channel' => $cover->alpha_channel,
//                    'animated'      => $cover->animated,
//                    'checksum'      => $cover->checksum,
//                    'height'        => $cover->height,
//                    'image_id'      => $cover->image_id,
//                    'url'           => $cover->url,
//                    'width'         => $cover->width,
//                    'created_at'    => $cover->created_at,
//                    'updated_at'    => $cover->updated_at,
//                ];
//            }
//        });

        return $game;
    }
}
