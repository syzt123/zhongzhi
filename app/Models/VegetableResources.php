<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class VegetableResources extends Model
{
    protected $table = 'vegetable_resources';
    public function getVegetableResourcesAttribute($value)
    {
        return asset($value);
    }
}
