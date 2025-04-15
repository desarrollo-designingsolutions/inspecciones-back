<?php

namespace App\Http\Resources\Vehicle;

use App\Http\Resources\BrandVehicle\BrandVehicleSelectInfiniteResource;
use App\Http\Resources\Client\ClientSelectInfiniteResource;
use App\Http\Resources\EmergencyElement\EmergencyElementSelectInfiniteResource;
use App\Http\Resources\TypeDocument\TypeDocumentSelectInfiniteResource;
use App\Http\Resources\TypeVehicle\TypeVehicleSelectInfiniteResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleFormResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $type_documents = null;

        if ($this->type_documents) {
            $type_documents = $this->type_documents->map(function ($item) {
                return [
                    'id' => $item->id,
                    'vehicle_id' => $item->vehicle_id,
                    'type_document_id' => new TypeDocumentSelectInfiniteResource($item->type_document),
                    'document_number' => $item->document_number,
                    'date_issue' => $item->date_issue,
                    'expiration_date' => $item->expiration_date,
                    'photo' => $item->photo,
                ];
            });
        }

        $emergenct_elements = null;

        if ($this->emergency_elements) {
            $emergenct_elements = $this->emergency_elements->map(function ($item) {

                return [
                    'id' => $item->id,
                    'vehicle_id' => $item->vehicle_id,
                    'emergency_element_id' => new EmergencyElementSelectInfiniteResource($item->emergency_element),
                    'quantity' => $item->quantity,
                    'expiration_date' => $item->expiration_date,
                ];
            });
        }

        $inspection_group_vehicle = [];

        if ($this->inspection_group_vehicle->isNotEmpty()) {
            $inspection_group_vehicle = $this->inspection_group_vehicle->map(function ($item) {
                return $item->id;
            });
        }

        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'license_plate' => $this->license_plate,
            'type_vehicle_id' => new TypeVehicleSelectInfiniteResource($this->type_vehicle),
            'date_registration' => $this->date_registration,
            'brand_vehicle_id' => new BrandVehicleSelectInfiniteResource($this->brand_vehicle),
            'engine_number' => $this->engine_number,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'model' => $this->model,
            'vin_number' => $this->vin_number,
            'load_capacity' => $this->load_capacity,
            'client_id' => new ClientSelectInfiniteResource($this->client),
            'gross_vehicle_weight' => $this->gross_vehicle_weight,
            'passenger_capacity' => $this->passenger_capacity,
            'number_axles' => $this->number_axles,
            'current_mileage' => $this->current_mileage,
            'have_trailer' => $this->have_trailer,
            'trailer' => $this->trailer,
            'vehicle_structure_id' => $this->vehicle_structure_id,
            'photo_front' => $this->photo_front,
            'photo_rear' => $this->photo_rear,
            'photo_right_side' => $this->photo_right_side,
            'photo_left_side' => $this->photo_left_side,
            'type_documents' => $type_documents,
            'emergency_elements' => $emergenct_elements,
            'type_groups' => $inspection_group_vehicle,

        ];
    }
}
