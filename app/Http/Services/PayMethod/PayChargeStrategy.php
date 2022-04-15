<?php

namespace App\Http\Services\PayMethod;
use Illuminate\Http\Request;

interface PayChargeStrategy
{
    public function payOrder(Request $request):string;
    public function notifyHandle(Request $request);
}
