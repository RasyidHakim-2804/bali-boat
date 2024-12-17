<?php

namespace App\Http\Resources\Product\FastBoat;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleAndFastBoatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
           'data' => [
                'schedules' => ScheduleResource::collection($this->schedules),
                'fast_boats' => FastBoatResource::collection($this->fast_boats),
            ],
            'links' => [
                'first' => $this->schedules->url(1),
                'last'  => $this->schedules->url($this->schedules->lastPage()),
                'prev'  => $this->schedules->previousPageUrl(),
                'next'  => $this->schedules->nextPageUrl(),
            ],
            'meta' => [
                'current_page'  => $this->schedules->currentPage(),
                'from'          => $this->schedules->firstItem(),
                'last_page'     => $this->schedules->lastPage(),
                'links'         => $this->schedules->linkCollection()->toArray(),
                'path'          => $this->schedules->path(),
                'per_page'      => $this->schedules->perPage(),
                'to'            => $this->schedules->lastItem(),
                'total'         => $this->schedules->total(),
            ]
        ];
    }
}
