<?php

namespace App\Http\Resources\Role;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleFormResource extends JsonResource
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
            'description' => $this->description,
            'company_id' => $this->company_id,
            'permissions' => $this->permissions->pluck('id'),
            'operator' => $this->operator,
            'mechanic' => $this->mechanic,
        ];
    }
}
