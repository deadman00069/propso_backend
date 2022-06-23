<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityProperty extends Model
{
    use HasFactory;
    protected $fillable = ['facility_id','property_id'];

}
