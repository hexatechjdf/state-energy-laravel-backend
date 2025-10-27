<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Http\Resources\DispositionResource;
use App\Models\Disposition;

class DispositionController extends Controller
{
    public function index()
    {
        $disposition = Disposition::all();
        return successResponse([
            'dispositions'  => DispositionResource::collection($disposition),
        ]);
    }

   
}
