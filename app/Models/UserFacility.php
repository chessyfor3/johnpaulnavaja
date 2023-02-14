<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFacility extends Model
{
    use HasFactory;
    protected $fillable = [ 'user_id', 'facility_id' ];
}
