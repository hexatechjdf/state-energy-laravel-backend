<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\OrderResource;
use App\Models\Disposition;

class DispositionController extends Controller
{
    public function index()
    {
        $orders = Disposition::all();
        return successResponse([
            'dispositions'  => OrderResource::collection($orders),
        ]);
    }

   
}
