<?php

namespace App\Http\Services\PayMethod;

use Illuminate\Http\Request;

interface PayChargeStrategy
{
    public function payOrder(Request $request): array;

    public function notifyHandle(Request $request);
}
