<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class VegetableResources extends Model
{
    protected $fillable = ['vegetable_grow','vegetable_resources','vegetable_resources_type'];
    protected $table = 'vegetable_resources';
    public $timestamps = false;
    public function getVegetableResourcesAttribute($value)
    {
        return asset($value);
    }
}
