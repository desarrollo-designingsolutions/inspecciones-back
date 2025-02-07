<?php

namespace App\Http\Resources\Vehicle;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleListResource extends JsonResource
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
            'license_plate' => $this->license_plate,
            'type_vehicle_name' => $this->type_vehicle?->name,
            'date_registration' => Carbon::parse($this->date_registration)->format('d-m-Y'),
            'model' => $this->model,
            'city_name' => $this->city?->name,
            'is_active' => $this->is_active,
        ];
    }
}
