<?php


namespace App\Http\Services;


use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class BaseService
{
    // 获取分页数据
    /**
     * @param int $count 总条数
     * @param int $page 当前页
     * @param int $pageSize 当前数量
     * @return array
     */
    static function getPageDataList($count = 0, $page = 1, $pageSize = 10, $data = []): array
    {
        return [
            "page" => ["count" => $count,
                "page" => $page,
                "page_size" => $pageSize,
                "total_page" => ceil($count / $pageSize)],
            "list" => $data,
        ];
    }


    static function getPageDataListByAdmin($join = [])
    {
        if (class_exists(get_called_class())) {
            $model = "App\Models\Admin\\" . str_replace("Service", '', (new \ReflectionClass(get_called_class()))->getShortName());
            if (class_exists($model)) {
                if (!empty($join)) {
                    $table = (new $model())->getTable();
                    return $model::join('member_info', $join['foreign_key'], '=', "{$join['table']}.id", $join['type'] ?? "inner")
                        ->paginate(Request::input('limit'), $join['field'] ?? "*");
                }
                return $model::paginate(Request::input('limit'));
            }
        }
        return false;
    }

    static function editModelByAdmin()
    {

        if (class_exists(get_called_class())) {
            $model = "App\Models\Admin\\" . str_replace("Service", '', (new \ReflectionClass(get_called_class()))->getShortName());
            if (class_exists($model)) {

                try {
                    DB::beginTransaction();
                    $model::where('id', Request::input('id'))
                        ->update(Request::input());
                    DB::commit();
                    return true;
                } catch (QueryException $exception) {
                    DB::rollBack();
                    return $exception->getMessage();
                }

            }
        }
        return false;
    }

    static function delModelByAdmin($id): bool|string
    {
        if (class_exists(get_called_class())) {
            $model = "App\Models\Admin\\" . str_replace("Service", '', (new \ReflectionClass(get_called_class()))->getShortName());
            if (class_exists($model)) {

                try {
                    DB::beginTransaction();
                    $model::where('id', $id)
                        ->delete();
                    DB::commit();
                    return true;
                } catch (QueryException $exception) {
                    DB::rollBack();
                    return $exception->getMessage();
                }

            }
        }
        return false;
    }

    static function getModelInfoById($id)
    {
        if (class_exists(get_called_class())) {
            $model = "App\Models\Admin\\" . str_replace("Service", '', (new \ReflectionClass(get_called_class()))->getShortName());
            if (class_exists($model)) {

                return $model::find($id);
            }
        }
        return false;

    }
}
