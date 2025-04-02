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
                    'original' => $item->inspectionDocumentVerifications?->original === true ? 1 : 0,
                    'expiration_date' => Carbon::Parse($item->expiration_date)->format('d-m-Y'),
                    'traffic_light_color' => $this->traffic_light_color($item->expiration_date),
                ];
            }),
        ];
    }

    private function traffic_light_color($expiration_date)
    {
        $expirationDate = Carbon::parse($expiration_date);
        $today = Carbon::now();
        $daysRemaining = $today->diffInDays($expirationDate, false); // false para que considere fechas pasadas como negativas
        if ($daysRemaining <= 5) {
            return 'error';
        } elseif ($daysRemaining <= 30) {
            return 'warning';
        } else {
            return 'success';
        }
    }
}
