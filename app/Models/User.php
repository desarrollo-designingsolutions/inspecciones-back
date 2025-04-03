<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\Cacheable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasPermissions, HasRoles, HasUuids,Notifiable,Searchable,Cacheable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Método de acceso para combinar nombre y apellido
    public function getFullNameAttribute()
    {
        return $this->name.' '.$this->surname;
    }

    public function getAllPermissionsAttribute()
    {
        return $this->getAllPermissions();
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function typeDocument()
    {
        return $this->belongsTo(UserTypeDocument::class);
    }

    public function license()
    {
        return $this->belongsTo(TypeLicense::class);
    }

    // Accesor para verificar si el usuario es operador
    public function getIsOperatorAttribute()
    {
        return $this->role ? $this->role->operator : false;
    }

    public function getIsMechanicAttribute()
    {
        return $this->role ? $this->role->mechanic : false;
    }
}
