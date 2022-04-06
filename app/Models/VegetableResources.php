<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class VegetableResources extends Model
{
    protected $table = 'vegetable_resources';
    protected $fillable = [
        'vegetable_grow',
        'vegetable_resources_type',
        'vegetable_resources'
    ];
    public $timestamps= false;
    public function getVegetableResourcesAttribute($value)
    {
        return asset('storage/'.$value);
    }
}
