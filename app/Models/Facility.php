<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'address',
        'city',
        'state',
        'zip',
        'contact',
    ];

    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'user_facilities');
    }

    public function user_facilities()
    {
        return $this->hasMany('App\Models\UserFacility');
    }
}
