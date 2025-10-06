<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\V1\DispositionStoreRequest;
use App\Http\Requests\Api\V1\DispositionUpdateRequest;
use App\Http\Resources\DispositionResource;
use App\Models\Adder;
use App\Models\Category;
use App\Models\Disposition;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\Facades\DataTables;

class DispositionController extends Controller
{

    public function index()
    {
        return view('admin.disposition.index');
    }
    public function getTableData(Request $req)
    {
        $items = Disposition::query();
        if (!empty($req->search['value'])) {
            $searchValue = $req->search['value'];
            $items->where(function ($query) use ($searchValue) {
                $query->where('name', 'like', "%{$searchValue}%");
            });
        }
        return DataTables::eloquent($items)
            ->addColumn('action', function ($item) {
                return '<button type="button" class="btn btn-sm btn-primary btn-edit-disposition"
                data-id="' . $item->id . '"  data-name="' . $item->name . '">
                <i class="fas fa-edit"></i> Edit
            </button>  <a href="javascript:void(0);" class="btn btn-sm btn-danger btn-delete-disposition"
               data-id="' . $item->id . '">
                <i class="fas fa-trash-alt"> Delete</i>
            </a>';
            })
            ->rawColumns(['action'])

            ->make(true);
    }
    public function store(DispositionStoreRequest $request)
    {
        $disposition = Disposition::create($request->validated());
        return successResponse(new DispositionResource($disposition), Response::HTTP_CREATED);
    }
    public function update(DispositionUpdateRequest $request)
    {
        $paylaod = $request->validated();
        $uuid = $paylaod['uuid'];
        unset($paylaod['uuid']);
        Disposition::where('id', $uuid)->update($paylaod);
        $disposition = Disposition::where('id', $uuid)->first();
        return successResponse(new DispositionResource($disposition), Response::HTTP_CREATED);
    }
    public function destroy($id)
    {
        $disposition = Disposition::find($id);
        if (!$disposition) {
            return errorResponse('Disposition not Found.');
        }
        try {
            $disposition->delete();
            return successResponse(null);
        } catch (\Exception $e) {
            return errorResponse('Failed to delete Disposition.');
        }
    }
}
