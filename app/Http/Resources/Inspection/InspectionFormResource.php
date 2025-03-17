<?php

namespace App\Http\Resources\Inspection;

use App\Http\Resources\PlateVehicle\PlateVehicleSelectInfiniteResource;
use App\Http\Resources\User\UserOperatorsSelectInfiniteResource;
use App\Models\InspectionTypeGroup;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InspectionFormResource extends JsonResource
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
            'user_operator_id' => new UserOperatorsSelectInfiniteResource($this->user_operator),
            'vehicle_id' => new PlateVehicleSelectInfiniteResource($this->vehicle),
            'inspection_date' => $this->inspection_date,
            'general_comment' => $this->general_comment,
            'type_documents' => $this->inspectionDocumentVerifications->map(function ($item) {
                return [
                    'id' => $item->id,
                    'inspection_id' => $item->inspection_id,
                    'vehicle_document_id' => $item->vehicle_document_id,
                    'original' => $item->original ? 1 : 0,
                ];
            }),
        ];

        $tabs = InspectionTypeGroup::select(['id'])
            ->with([
                'inspectionTypeInputs:id,inspection_type_group_id',
                'inspectionTypeInputs.inspectionInputResponses:id,inspection_type_input_id,response,observation,inspection_id',
                'inspectionTypeInputs.inspectionInputResponses' => function ($query) {
                    $query->where('inspection_id', $this->id);
                },
            ])
            ->where('inspection_type_id', $this->inspection_type_id)->get();

        foreach ($tabs as $tab) {
            if (isset($tab['inspectionTypeInputs']) && count($tab['inspectionTypeInputs']) > 0) {
                foreach ($tab['inspectionTypeInputs'] as $input) {
                    $inspection_input_responses = $input['inspectionInputResponses']->first();
                    if ($this->inspection_type_id == 1) {
                        $decodedResponse = json_decode($inspection_input_responses->response, true);

                        $info[$input['id']]['value'] = $decodedResponse['value'] ?? $inspection_input_responses->response;
                    } else {
                        $info[$input['id']]['value'] = $inspection_input_responses->response;
                        $info[$input['id']]['observation'] = $inspection_input_responses?->observation;
                    }
                }
            }
        }

        return $info;
    }
}
