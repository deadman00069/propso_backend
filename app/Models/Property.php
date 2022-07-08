<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function Symfony\Component\Translation\t;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'location_id',
        'property_category_id',
        'property_type_id',
        'title',
        'description',
        'short_summery',
        'area',
        'how_many_beds',
        'how_many_bathroom',
        'latitude',
        'longitude',
        'price',
        'discount',
    ];

    public function images()
    {
        return $this->hasMany(PropertyImage::class);
    }

    public function videos()
    {
        return $this->hasMany(PropertyVideo::class);
    }

    public function facility()
    {
        return $this->belongsToMany(Facility::class, 'facility_properties', 'property_id', 'facility_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function propertyCategory()
    {
        return $this->belongsTo(PropertyCategory::class);
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }
}
