<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class InvoiceResource extends JsonResource
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
            'reservation' => (new ReservationResource($this->reservation))->toArray($request),
            'subject' => $this->subject,
            'pdf_path' => $this->pdf_path,
            'billing_start_date' => Carbon::parse($this->billing_start_date)->format('d/m/Y'),
            'billing_end_date' => Carbon::parse($this->billing_end_date)->format('d/m/Y'),
            'description' => $this->description,
            'issued_at' => $this->issued_at ? Carbon::parse($this->issued_at)->format('d/m/Y H:i:s') : null,
            'paid_at' => $this->paid_at ? Carbon::parse($this->paid_at)->format('d/m/Y H:i:s') : null,
            'status' => $this->status,
            'status_text' => $this->status_text, // Inclure le texte du statut
            'created_at' => $this->created_at->format('d/m/Y H:i:s'),
            'updated_at' => $this->updated_at->format('d/m/Y H:i:s'),
        ];
    }
}
