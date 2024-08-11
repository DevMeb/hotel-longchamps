<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'rent' => $this->convertCentsToEuros($this->rent), // Convertir en euros pour l'affichage
            'created_at' => $this->created_at->format('d/m/Y H:i:s'),
            'updated_at' => $this->updated_at->format('d/m/Y H:i:s'),
        ];
    }

    private function convertCentsToEuros($amount): string
    {
        return number_format($amount / 100, 2, ',', ' ') . ' €'; // Convertir en euros avec formatage
    }
}
