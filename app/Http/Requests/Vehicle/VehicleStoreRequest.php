<?php

namespace App\Http\Requests\Vehicle;

use App\Helpers\Constants;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class VehicleStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            //Modulo 1
            'company_id' => 'required',
            'license_plate' => 'required|max:6|unique:vehicles,license_plate,' . $this->id . ',id,company_id,' . $this->company_id,
            'type_vehicle_id' => 'required',
            'date_registration' => 'required|date|before_or_equal:today',
            'brand_vehicle_id' => 'required',
            'engine_number' => 'required|max:255',
            'state_id' => 'required',
            'city_id' => 'required',
            'model' => 'required|numeric',
            'vin_number' => 'required|max:255',
            'load_capacity' => 'required|numeric|min:1',
            'client_id' => 'required',
            'gross_vehicle_weight' => 'required|numeric|min:1',
            'passenger_capacity' => 'required|numeric|min:1',
            'number_axles' => 'required|numeric|min:1',
            'current_mileage' => 'required|numeric|min:1',
            'have_trailer' => 'required',
            'vehicle_structure_id' => 'required',
            //Modulo 3
            'photo_front' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!is_string($value) || !preg_match('/\.(jpg|png)$/i', $value)) {
                        if (!$value instanceof \Illuminate\Http\UploadedFile || !in_array($value->getClientOriginalExtension(), ['jpg', 'png'])) {
                            $fail('El archivo debe ser una imagen válida (JPG o PNG) o una ruta válida.');
                        }
                    }
                },
            ],
            'photo_rear' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!is_string($value) || !preg_match('/\.(jpg|png)$/i', $value)) {
                        if (!$value instanceof \Illuminate\Http\UploadedFile || !in_array($value->getClientOriginalExtension(), ['jpg', 'png'])) {
                            $fail('El archivo debe ser una imagen válida (JPG o PNG) o una ruta válida.');
                        }
                    }
                },
            ],
            'photo_right_side' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!is_string($value) || !preg_match('/\.(jpg|png)$/i', $value)) {
                        if (!$value instanceof \Illuminate\Http\UploadedFile || !in_array($value->getClientOriginalExtension(), ['jpg', 'png'])) {
                            $fail('El archivo debe ser una imagen válida (JPG o PNG) o una ruta válida.');
                        }
                    }
                },
            ],
            'photo_left_side' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!is_string($value) || !preg_match('/\.(jpg|png)$/i', $value)) {
                        if (!$value instanceof \Illuminate\Http\UploadedFile || !in_array($value->getClientOriginalExtension(), ['jpg', 'png'])) {
                            $fail('El archivo debe ser una imagen válida (JPG o PNG) o una ruta válida.');
                        }
                    }
                },
            ],
        ];

        if ($this->have_trailer === true) {
            $rules['trailer'] = 'required|max:255';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'company_id.required' => 'El campo es obligatorio',
            'license_plate.required' => 'El campo es obligatorio',
            'license_plate.max' => 'El campo no debe tener mas de 6 caracteres',
            'license_plate.unique' => 'La matricula ya existe',
            'type_vehicle_id.required' => 'El campo es obligatorio',
            'date_registration.required' => 'El campo es obligatorio',
            'date_registration.date' => 'El campo debe ser una fecha valida',
            'date_registration.before_or_equal' => 'La fecha no puede ser mayor a la fecha actual',
            'brand_vehicle_id.required' => 'El campo es obligatorio',
            'engine_number.required' => 'El campo es obligatorio',
            'engine_number.max' => 'El campo no debe tener mas de 255 caracteres',
            'state_id.required' => 'El campo es obligatorio',
            'city_id.required' => 'El campo es obligatorio',
            'model.required' => 'El campo es obligatorio',
            'model.numeric' => 'El campo debe ser un numero',
            'vin_number.required' => 'El campo es obligatorio',
            'vin_number.max' => 'El campo no debe tener mas de 255 caracteres',
            'load_capacity.required' => 'El campo es obligatorio',
            'load_capacity.numeric' => 'El campo debe ser un numero',
            'load_capacity.min' => 'El campo debe ser mayor a 0',
            'client_id.required' => 'El campo es obligatorio',
            'gross_vehicle_weight.required' => 'El campo es obligatorio',
            'gross_vehicle_weight.numeric' => 'El campo debe ser un numero',
            'gross_vehicle_weight.min' => 'El campo debe ser mayor a 0',
            'passenger_capacity.required' => 'El campo es obligatorio',
            'passenger_capacity.numeric' => 'El campo debe ser un numero',
            'passenger_capacity.min' => 'El campo debe ser mayor a 0',
            'number_axles.required' => 'El campo es obligatorio',
            'number_axles.numeric' => 'El campo debe ser un numero',
            'number_axles.min' => 'El campo debe ser mayor a 0',
            'current_mileage.required' => 'El campo es obligatorio',
            'current_mileage.numeric' => 'El campo debe ser un numero',
            'current_mileage.min' => 'El campo debe ser mayor a 0',
            'have_trailer.required' => 'El campo es obligatorio',
            'trailer.required' => 'El campo es obligatorio',
            'trailer.max' => 'El campo no debe tener mas de 255 caracteres',
            'vehicle_structure_id.required' => 'El campo es obligatorio',

            'photo_front.required' => 'El campo es obligatorio',
            'photo_front.extensions' => 'El archivo debe ser de tipo PNG, JPG.',
            'photo_rear.required' => 'El campo es obligatorio',
            'photo_rear.extensions' => 'El archivo debe ser de tipo PNG, JPG.',
            'photo_right_side.required' => 'El campo es obligatorio',
            'photo_right_side.extensions' => 'El archivo debe ser de tipo PNG, JPG.',
            'photo_left_side.required' => 'El campo es obligatorio',
            'photo_left_side.extensions' => 'El archivo debe ser de tipo PNG, JPG.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'have_trailer' => $this->have_trailer == 'true' ? true : false,
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
