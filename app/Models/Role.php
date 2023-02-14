<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $fillable = [ 'name' ];

    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'user_roles');
    }

    public function user_roles()
    {
        return $this->hasMany('App\Models\UserRole');
    }
}
