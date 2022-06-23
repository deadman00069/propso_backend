<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Model
{
    use HasFactory, HasApiTokens, HasRoles;

    protected $fillable = ['name', 'email', 'password', 'phone_no', 'role', 'avatar_url'];
    protected $hidden = ['password'];
}
