<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Http\Resources\LenderResource;
use App\Models\Lender;

class LenderController extends Controller
{

    public function index()
    {
        $orders = Lender::all();
        return successResponse([
            'lenders'  => LenderResource::collection($orders),
        ]);
    }
}
