<?php

namespace App\Http\Resources\Maintenance;

use App\Http\Resources\User\UserMechanicsSelectInfiniteResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class MaintenancePaginateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $mechanic_name = User::find($this->user_mechanic_id);
        $mechanic_name = $mechanic_name?->full_name;

        $inspector_name = User::find($this->user_inspector_id);
        $inspector_name = $inspector_name?->full_name;

        return [
            'id' => $this->id,
            'vehicle_license_plate' => $this->vehicle?->license_plate,
            'maintenance_date' => Carbon::parse($this->maintenance_date)->format('d-m-Y'),
            'vehicle_brand_name' => $this->vehicle?->brand_vehicle?->name,
            'vehicle_model' => $this->vehicle?->model,
            'maintenance_type_id' => $this->maintenance_type_id,
            'maintenance_type_name' => $this->maintenanceType?->name,
            'user_inspector_full_name' => $inspector_name,
            'user_mechanic_full_name' => $mechanic_name,
            'status' => getResponseStatus($this->status),
        ];
    }
}
