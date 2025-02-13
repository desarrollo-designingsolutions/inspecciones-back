<?php

namespace App\Http\Resources\Maintenance;

use App\Http\Resources\PlateVehicle\PlateVehicleSelectInfiniteResource;
use App\Http\Resources\User\UserInspectorsSelectInfiniteResource;
use App\Http\Resources\User\UserMechanicsSelectInfiniteResource;
use App\Http\Resources\User\UserOperatorsSelectInfiniteResource;
use App\Models\MaintenanceTypeGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceFormResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $info = [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'city_id' => $this->city_id,
            'state_id' => $this->state_id,
            'user_mechanic_id' => new UserMechanicsSelectInfiniteResource(User::find($this->user_mechanic_id)),
            'user_operator_id' => new UserOperatorsSelectInfiniteResource(User::find($this->user_operator_id)),
            'user_inspector_id' => new UserInspectorsSelectInfiniteResource(User::find($this->user_inspector_id)),
            'vehicle_id' => new PlateVehicleSelectInfiniteResource($this->vehicle),
            'maintenance_date' => $this->maintenance_date,
            'mileage' => $this->mileage,
            'general_comment' => $this->general_comment,
            'status' => $this->status,
        ];

        $tabs = MaintenanceTypeGroup::select(['id'])
            ->with([
                'maintenanceTypeInputs:id,maintenance_type_group_id',
                'maintenanceTypeInputs.maintenanceInputResponses:id,maintenance_type_input_id,maintenance_id,type,type_maintenance,comment',
                'maintenanceTypeInputs.maintenanceInputResponses' => function ($query) {
                    $query->where('maintenance_id', $this->id);
                },
            ])
            ->where('maintenance_type_id', $this->maintenance_type_id)->get();

        foreach ($tabs as $tab) {
            if (isset($tab['maintenanceTypeInputs']) && count($tab['maintenanceTypeInputs']) > 0) {
                foreach ($tab['maintenanceTypeInputs'] as $input) {
                    $maintenance_input_responses = $input['maintenanceInputResponses']->first();
                    $info[$input['id']] = [
                        'type' => $maintenance_input_responses ? $maintenance_input_responses?->type : null,
                        'type_maintenance' => $maintenance_input_responses ? $maintenance_input_responses->type_maintenance : null,
                        'comment' => $maintenance_input_responses ? $maintenance_input_responses->comment : null,
                    ];
                }
            }
        }

        return $info;
    }
}
