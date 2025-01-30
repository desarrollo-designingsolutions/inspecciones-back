<?php

namespace App\Http\Requests\Inspection;

use App\Helpers\Constants;
use App\Models\InspectionTypeGroup;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class InspectionStoreRequest extends FormRequest
{
    private $tabs;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'company_id' => 'required',
            'city_id' => 'required',
            'state_id' => 'required',
            'user_id' => 'required',
            'vehicle_id' => 'required',
            'inspection_date' => 'required',
        ];


        foreach ($this->tabs as $tab) {
            if (isset($tab['inspectionTypeInputs']) && count($tab['inspectionTypeInputs']) > 0) {
                foreach ($tab['inspectionTypeInputs'] as $input) {
                    $rules[$input['id']] = 'required';
                }
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [
            'company_id.required' => 'El campo es obligatorio',
            'city_id.required' => 'El campo es obligatorio',
            'state_id.required' => 'El campo es obligatorio',
            'user_id.required' => 'El campo es obligatorio',
            'vehicle_id.required' => 'El campo es obligatorio',
            'inspection_date.required' => 'El campo es obligatorio',
        ];

        foreach ($this->tabs as $tab) {
            if (isset($tab['inspectionTypeInputs']) && count($tab['inspectionTypeInputs']) > 0) {
                foreach ($tab['inspectionTypeInputs'] as $input) {
                    $messages[$input['id'].'.required'] = 'El campo es obligatorio';
                }
            }
        }

        return $messages;
    }

    protected function prepareForValidation(): void
    {
        $this->tabs = InspectionTypeGroup::select(['id'])->with(['inspectionTypeInputs:id,inspection_type_group_id'])->where('inspection_type_id', $this->inspection_type_id)->get();

        $this->merge([
            'user_id' => is_array($this->user_id) && isset($this->user_id['value']) ? $this->user_id['value'] : $this->user_id,
            'vehicle_id' => is_array($this->vehicle_id) && isset($this->vehicle_id['value']) ? $this->vehicle_id['value'] : $this->vehicle_id,

        ]);
    }

    public function failedValidation(Validator $validator)
    {

        throw new HttpResponseException(response()->json([
            'code' => 422,
            'message' => Constants::ERROR_MESSAGE_VALIDATION_BACK,
            'errors' => $validator->errors(),
        ], 422));
    }
}
