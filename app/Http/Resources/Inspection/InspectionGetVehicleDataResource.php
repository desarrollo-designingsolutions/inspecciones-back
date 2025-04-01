<?php

namespace App\Http\Resources\Inspection;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class InspectionGetVehicleDataResource extends JsonResource
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
            'brand_vehicle_name' => $this->brand_vehicle?->name,
            'model' => $this->model,
            'vehicle_structure_name' => $this->vehicle_structure?->name,
            'type_documents' => $this->type_documents->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type_document_name' => $item->type_document?->name,
                    'document_number' => $item->document_number,
                    'original' => $item->inspectionDocumentVerifications?->original,
                    'expiration_date' => Carbon::Parse($item->expiration_date)->format('d-m-Y'),
                ];
            }),
        ];
    }
}
