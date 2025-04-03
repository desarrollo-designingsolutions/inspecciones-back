<?php

namespace App\Models;

use App\Traits\Cacheable;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use Cacheable;

    protected $casts = [
        'order' => 'integer',
    ];

    public function children()
    {
        return $this->hasMany(Menu::class, 'father', 'id')->orderBy('order', 'asc');
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'menu_id', 'id');
    }
}
