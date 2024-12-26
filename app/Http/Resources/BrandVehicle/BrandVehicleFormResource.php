<?php

namespace App\Http\Resources\BrandVehicle;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandVehicleFormResource extends JsonResource
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
            'name' => $this->name,
            'company_id' => $this->company_id,
        ];
    }
}