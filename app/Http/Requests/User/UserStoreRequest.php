<?php

namespace App\Http\Requests\User;

use App\Helpers\Constants;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

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
            'email' => 'email|regex:"^[^@]+@[^@]+\.[a-zA-Z]{2,}$"|required|unique:users,email,'.$this->id.',id,company_id,'.$this->company_id,
            'company_id' => 'required',
            'role_id' => 'required',
        ];

        $operator = (bool) DB::table('roles')
            ->where('id', $this->role_id)
            ->value('operator');

        if ($operator === true) {
            $rules['type_document_id'] = 'required';
            $rules['document'] = 'required|unique:users,document,'.$this->id.',id,company_id,'.$this->company_id;
            $rules['type_license_id'] = 'required';
            $rules['license'] = 'required|unique:users,license,'.$this->id.',id,company_id,'.$this->company_id;
            $rules['expiration_date'] = 'required|date';
        }

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
            'email.email' => 'El campo debe ser un correo valido',
            'email.required' => 'El campo es obligatorio',
            'email.unique' => 'El correo electrónico ya existe',
            'email.regex' => 'El email debe contener un @ y una extensión ejemplo(.com)',
            'password.required' => 'El campo es obligatorio',
            'company_id.required' => 'El campo es obligatorio',
            'role_id.required' => 'El campo es obligatorio',
            'type_document_id.required' => 'El campo es obligatorio',
            'document.required' => 'El campo es obligatorio',
            'document.unique' => 'El documento ya existe',
            'type_license_id.required' => 'El campo es obligatorio',
            'license.required' => 'El campo es obligatorio',
            'license.unique' => 'La licencia ya existe',
            'expiration_date.required' => 'El campo es obligatorio',
            'expiration_date.date' => 'El campo debe ser una fecha',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'role_id' => is_array($this->role_id) && isset($this->role_id['value']) ? $this->role_id['value'] : $this->role_id,
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
