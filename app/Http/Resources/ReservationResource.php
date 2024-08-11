<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'renter' => new RenterResource($this->renter),
            'room' => new RoomResource($this->room),
            'start_date' => Carbon::parse($this->start_date)->format('d/m/Y'),
            'end_date' => $this->end_date ? Carbon::parse($this->end_date)->format('d/m/Y') : null,
            'created_at' => $this->created_at->format('d/m/Y H:i:s'),
            'updated_at' => $this->updated_at->format('d/m/Y H:i:s'),
        ];
    }
}
