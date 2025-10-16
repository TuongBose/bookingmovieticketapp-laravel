<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->userid ?? $this->user_id,
            'showTimeId' => $this->showtimeid ?? $this->show_time_id,
            'bookingdate' => $this->bookingdate,
            'totalprice' => $this->totalprice,
            'paymentmethod' => $this->paymentmethod,
            'paymentstatus' => $this->paymentstatus,
            'isactive' => (bool) ($this->isactive ?? $this->is_active),
        ];
    }
}
