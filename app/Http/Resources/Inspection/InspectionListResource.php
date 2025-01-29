<?php

namespace App\Http\Resources\Inspection;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InspectionListResource extends JsonResource
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
            'vehicle_license_plate' => $this->vehicle?->license_plate,
            'inspection_date' => Carbon::parse($this->inspection_date)->format('d-m-Y'),
            'vehicle_brand_name' =>  $this->vehicle?->brand_vehicle?->name,
            'vehicle_model' => $this->vehicle?->model,
            'inspection_type_id' => $this->inspection_type_id,
            'inspection_type_name' => $this->inspectionType?->name,
            'user_full_name' => $this->user?->full_name,
        ];
    }
}
