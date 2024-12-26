<?php

namespace App\Http\Requests\TypeVehicle;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class TypeVehicleStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'company_id' => 'required',
            'name' => 'required|unique:clients,name,'.$this->id.',id,company_id,'.$this->company_id,
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'company_id.required' => 'El campo es obligatorio',
            'name.required' => 'El campo es obligatorio',
            'name.unique' => 'El nombre ya está en uso',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([]);
    }

    public function failedValidation(Validator $validator)
    {

        throw new HttpResponseException(response()->json([
            'code' => 422,
            'message' => 'Hubo un error en la validación del formulario',
            'errors' => $validator->errors(),
        ], 422));
    }
}
