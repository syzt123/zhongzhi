<?php


namespace App\Models\Admin;


use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
    const CREATED_AT = 'create_time';
    const UPDATED_AT = null;
    protected $dateFormat = 'U';
    protected $casts = [
        'create_time' => 'datetime:Y-m-d H:i:s',
    ];
//    public function page($pageOption = ["limit" => 15, "page" => 1])
//    {
//        $count = $this->toBase()->getCountForPagination();;
//        $data = $this
//            ->forPage($pageOption['page'], $pageOption['limit'])
//            ->get();
//        $code = 0;
//        return compact('data', 'count', 'code');
//    }
}
