<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Role extends Model
{
    protected $fillable = ['role_name', 'permissions'];
    
    protected $casts = [
        'permissions' => 'array', // agar json otomatis didecode ke array
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
