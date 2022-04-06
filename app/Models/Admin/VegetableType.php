<?php


namespace App\Models\Admin;

use App\Models\VegetableType as Base;
use Illuminate\Support\Facades\Storage;
use mysql_xdevapi\Exception;

class VegetableType extends Base
{
    protected $table = "vegetable_type";
    const CREATED_AT = 'create_time';
    const UPDATED_AT = null;
    protected $dateFormat = 'U';
    protected $fillable = [
        'f_price',
        'grow_1',
        'grow_2',
        'grow_3',
        'grow_4',
        'grow_5',
        'status',
        'storage_time',
        'v_price',
        'v_type',
    ];
    protected $casts = [
        'create_time' => 'datetime:Y-m-d H:i:s',
    ];


    public function getVPriceAttribute($value)
    {
        return bcdiv($value, 100, 2);
    }

    public function getFPriceAttribute($value)
    {
        return bcdiv($value, 100, 2);
    }

    public function getStatusAttribute($value)
    {
        $arr = ["未知", "可种植", "不可种植"];
        return $arr[$value];
    }

    static function editByAdmin()
    {
        $model = self::with([]);
        $vegetableType = $model
            ->where('id', '=', request()->input('id'))
            ->first();
        if (!$vegetableType) {
            throw new Exception('未找到相关蔬菜');
        } else {
            $upResources = array();
            $data = request()->input();
            foreach ($data as $key => $value) {
                if (str_contains($key, 'img_grow_')) {
                    $upResources[str_replace('img_grow_', '', $key)] = $value;
                    unset($data[$key]);
                }
            }
            foreach ($upResources as $key => $val) {
                if (Storage::disk('local')->get($val)) {
                    $new_path = str_replace('tmp', 'public/vegetable_resources', $val);
                    if (Storage::disk('local')->move($val, $new_path)) {
                        $vegetableTypeGrow = $vegetableType
                            ->vegetableResources()
                            ->where([
                                'vegetable_type_id' => request()->input('id'),
                                'vegetable_grow' => $key,
                                'vegetable_resources_type' => 1
                            ])
                            ->first();
                        if ($vegetableTypeGrow && Storage::disk('local')->delete($vegetableTypeGrow->vegetable_resources)) {
                            $vegetableTypeGrow->vegetable_resources = $new_path;
                            $vegetableTypeGrow->save();
                        } else {
                            $vegetableType
                                ->vegetableResources()
                                ->create([
                                    'vegetable_type_id' => request()->input('id'),
                                    'vegetable_grow' => $key,
                                    'vegetable_resources_type' => 1,
                                    'vegetable_resources' => $new_path,
                                ]);
                        }
                    }
                }
            }
            return $vegetableType->update($data, ['id' => request()->id]);
        }
    }

}
