<?php

namespace App\Http\Requests\User;

use App\Helpers\Constants;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {

        $rules = [
            'name' => 'required',
            'surname' => 'required',
            'email' => 'email|required|unique:users,email,'.$this->id,
            'company_id' => 'required',
        ];

        if (! $this->id) {
            $rules['password'] = 'required';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El campo es obligatorio',
            'surname.required' => 'El campo es obligatorio',
            'email.required' => 'El campo es obligatorio',
            'email.unique' => 'El correo electrónico ya existe',
            'password.required' => 'El campo es obligatorio',
            'company_id.required' => 'El campo es obligatorio',
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
            'message' => 'Se evidencia algunos errores',
            'errors' => $validator->errors(),
        ], 422));
    }
}
