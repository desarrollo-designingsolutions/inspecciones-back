<?php

namespace App\Http\Requests\TypeDocument;

use App\Helpers\Constants;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class TypeDocumentStoreRequest extends FormRequest
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
            'name' => [
                'required',
                Rule::unique('type_documents', 'name')
                        ->ignore($this->id) // Ignorar el ID actual si es una actualización
                        ->where(function ($query) {
                            $query->where('company_id', $this->company_id) // Filtrar por empresa
                                  ->whereNull('deleted_at'); // Excluir registros eliminados
                        }),
            ],
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'company_id.required' => 'El campo es obligatorio',
            'name.required' => 'El campo es obligatorio',
            'name.unique' => 'El nombre ya está registrado',
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
            'message' => Constants::ERROR_MESSAGE_VALIDATION_BACK,
            'errors' => $validator->errors(),
        ], 422));
    }
}
