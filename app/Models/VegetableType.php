<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\Self_;

//蔬菜种类表
class VegetableType extends Model
{
    protected $table = 'vegetable_type';
    protected $dateFormat = 'U';
    protected $fillable = [
        "f_price",
        "grow_1",
        "grow_2",
        "grow_3",
        "grow_4",
        "grow_5",
        "status",
        "storage_time",
        "v_price",
        "v_type"
    ];

    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    // 新增
    static function addVegetableType($data)
    {
        $resources = array();
        $model = new static;
        DB::beginTransaction();
        $model->fill($data);
        $model->save();
        $movedFiles = array();
        foreach ($data as $key => $value) {
            if (!in_array($key, $model->getFillable()) && $value && is_numeric(str_replace('img_grow_', '', $key))) {
                $newFile = str_replace('tmp/', 'public/vegetable_resources/', $value);

                if (Storage::move($value, $newFile)) {
                    $movedFiles[] = $newFile;
                    $resources[] = new VegetableResources([
                        'vegetable_grow' => str_replace('img_grow_', '', $key),
                        'vegetable_resources_type' => 1,
                        'vegetable_resources' => str_replace('public/', '', $newFile)
                    ]);
                } else {
                    foreach ($movedFiles as $file) {
                        Storage::disk('public')->delete(str_replace('public/', '', $file));
                    }
                    DB::rollBack();
                    return '请重新选择各生长阶段图片';
                }

            }
        }
        $model->vegetableResources()->saveMany($resources);
        DB::commit();
        Cache::forget('new_file');
        return true;
    }

    // 查询
    static function getVegetableTypeList($data = []): array
    {
        $lists = self::with([]);
        $page = 1;
        $pageSize = 10;
        $sort = 'desc';// desc asc
        if (isset($data["page"]) && (int)$data["page"] > 0) {
            $page = $data["page"];
            unset($data["page"]);
        }
        if (isset($data["page_size"])) {
            $pageSize = $data["page_size"];
            unset($data["page_size"]);
        }
        if (count($data)) {
            $lists = $lists->where($data);
        }
        $skipNums = ($page - 1) * $pageSize;
        $lists = $lists->skip($skipNums)->limit($pageSize)->orderBy("id", $sort)->get();

        if ($lists) {
            return $lists->toArray();
        }
        return [];
    }

    // 删除
    static function delVegetableType($id, $data = []): int
    {
        $model = self::with([])->where("id", $id);
        if (count($data)) {
            $model = $model->where($data);
        }
        return $model->delete();
    }

    // 查询
    static function findVegetableTypeInfoById($id, $data = []): array
    {
        $model = self::with(['vegetableKinds'])->where("id", $id);
        if (count($data)) {
            $model = $model->where($data);
        }
        $rs = $model->first();
        if ($rs != null) {
            return $rs->toArray();
        }
        return [];
    }

    // 获取种类总数
    static function getVegetableTypeNums($data = []): int
    {
        if (isset($data["page"]) && (int)$data["page"] > 0) {
            unset($data["page"]);
        }
        if (isset($data["page_size"])) {
            unset($data["page_size"]);
        }
        $model = self::with([]);
        if (count($data)) {
            $model = $model->where($data);
        }
        return $model->count();
    }

    public function vegetableResources()
    {
        return $this->hasMany(VegetableResources::class);
    }

    // 蔬菜种子
    static function getVegetableTypeSeed()
    {
        $model = self::with([]);
        $vegetables = $model
            ->where('status', '=', 1)
            ->get();
        foreach ($vegetables as $vegetable) {
            $vegetable->vegetable_img = $vegetable
                ->vegetableResources()
                ->where('vegetable_resources.vegetable_grow', '=', 1)
                ->where('vegetable_resources.vegetable_resources_type', '=', 1)
                ->value('vegetable_resources');
        }
        return $vegetables;
    }

    public function memberVegetables()
    {
        return $this->hasMany(MemberVegetable::class,);
    }

    public function vegetableKinds()
    {
        return $this->hasOne(VegetableKinds::class, 'id', 'v_kinds_id');
    }
}
