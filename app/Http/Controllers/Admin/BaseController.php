<?php


namespace App\Http\Controllers\Admin;


class BaseController
{
    public function success($data = [], $message = 'ok', $code = 1)
    {
        return response()->json(compact('data', 'message', 'code'));
    }

    public function error($message = 'error', $data = [], $code = 0)
    {
        return response()->json(compact('data', 'message', 'code'));
    }
}
