<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserFormResource extends JsonResource
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
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'role_id' => [
                'value' => $this->role_id,
                'title' => $this->role?->description,
                'operator' => $this->role?->operator,
            ],
            'company_id' => $this->company_id,
            'type_document_id' => $this->type_document_id,
            'document' => $this->document,
            'type_license_id' => $this->type_license_id,
            'license' => $this->license,
            'expiration_date' => $this->expiration_date,
        ];
    }
}
