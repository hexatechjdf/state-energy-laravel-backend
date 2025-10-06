<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\V1\LenderStoreRequest;
use App\Http\Requests\Api\V1\LenderUpdateRequest;
use App\Http\Resources\LenderResource;
use App\Models\Lender;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\Facades\DataTables;
use App\Services\FileUploadService;

class LenderController extends Controller
{

    public function index()
    {
        return view('admin.lender.index');
    }
    public function getTableData(Request $req)
    {
        $items = Lender::query();
        if (!empty($req->search['value'])) {
            $searchValue = $req->search['value'];
            $items->where(function ($query) use ($searchValue) {
                $query->where('url', 'like', "%{$searchValue}%");
            });
        }
        return DataTables::eloquent($items)
            ->addColumn('logo', function ($item) {
                return $item->logo_url
                    ? '<img class="table-image" src="' . $item->logo_url . '" height="50"/>'
                    : '-';
            })
            ->addColumn('action', function ($item) {
                return '<button type="button" class="btn btn-sm btn-primary btn-edit-lender"
                data-id="' . $item->id . '"  data-url="' . $item->url . '" data-logo="' . $item->logo_url . '">
                <i class="fas fa-edit"></i> Edit
            </button>  <a href="javascript:void(0);" class="btn btn-sm btn-danger btn-delete-lender"
               data-id="' . $item->id . '">
                <i class="fas fa-trash-alt"> Delete</i>
            </a>';
            })
            ->rawColumns(['action', 'logo'])

            ->make(true);
    }
    public function store(LenderStoreRequest $request, FileUploadService $fileService)
    {
        $paylaod = $request->validated();
        $lender = new Lender();
        if ($request->hasFile('logo')) {
            $lender->logo = $fileService->upload(
                $request->file('logo'),
                'lender/logos',
                'public'
            );
        }
        $lender->url = $paylaod['url'];
        $lender->save();
        return successResponse(new LenderResource($lender), Response::HTTP_CREATED);
    }
    public function update(LenderUpdateRequest $request, FileUploadService $fileService)
    {
        $paylaod = $request->validated();
        $lender = Lender::where('id',$paylaod['uuid'])->first();
        if ($request->hasFile('logo')) {
            if ($lender->logo) {
                $fileService->delete($lender->logo, 'public');
            }
            $lender->logo = $fileService->upload(
                $request->file('logo'),
                'lender/logos',
                'public'
            );
        }
        $lender->url = $paylaod['url'];
        $lender->save();
        return successResponse(new LenderResource($lender), Response::HTTP_CREATED);
    }
    public function destroy($id)
    {
        $lender = Lender::find($id);
        if (!$lender) {
            return errorResponse('Lender not Found.');
        }
        try {
            $lender->delete();
            return successResponse(null);
        } catch (\Exception $e) {
            return errorResponse('Failed to delete Lender.');
        }
    }
}
