<?php

namespace App\Http\Controllers;

use App\Helpers\CRM;
use App\Http\Requests\Api\V1\UserStoreRequest;
use App\Http\Resources\UserResource;
use App\Jobs\SendGhlWelcomeEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Arr;

class AdminController extends Controller
{
    public function store(UserStoreRequest $request)
    {
        $user = User::create($request->validated());
        dispatch(new SendGhlWelcomeEmail($user, $request->password))->onQueue(config('app.env'));
        return successResponse(new UserResource($user), Response::HTTP_CREATED);
    }
    public function update(Request $request)
    {
        // Validate request regardless of AJAX or not
        $validated = $request->validate([
            'uuid' => 'required',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'user_id' => 'required',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        try {
            $user = User::findOrFail($request->uuid);
            $user->fill(Arr::except($validated, ['password', 'password_confirmation']));

            // Only update password if filled
            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
            }

            $user->save();

            if ($request->ajax()) {
                return successResponse(new UserResource($user->refresh()));
            }

            $message = !empty($request->password)
                ? 'User profile and password updated successfully'
                : 'User profile updated successfully';

            return response()->json(['status' => 'Success', 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'Error', 'message' => $e->getMessage()], 500);
        }
    }
    public function dashboard(Request $request)
    {
        $user = loginUser();
        return view('location.dashboard', compact('user'));
    }
    public function index()
    {
        $location_id = getSettingValue(\Auth::user()->id, 'location_id', '');
        return view('admin.user.index')->with(['location_id' => $location_id]);
    }
    public function getTableData(Request $req)
    {
        $items = User::where('role_id', User::ROLE_LOCATION);
        // Apply search filtering for specific columns
        if (!empty($req->search['value'])) {
            $searchValue = $req->search['value'];
            $items = $items->where(function ($query) use ($searchValue) {
                $query->orWhere('first_name', 'like', "%{$searchValue}%")
                    ->orWhere('last_name', 'like', "%{$searchValue}%");
            });
        }
        return DataTables::eloquent($items)
            ->editColumn('action', function ($item) {
                $location_id = getSettingValue(\Auth::user()->id, 'location_id', '');
                return '<a href="javascript:void(0);" class="text-primary btn-edit-user"
               data-id="' . $item->id . '"
               data-user-id="' . $item->user_id . '"
               data-location-id="' . $location_id . '" >
                <i class="fas fa-edit fa-2x"></i>
            </a>
             <a href="javascript:void(0);" class="text-danger btn-delete-user"
               data-id="' . $item->id . '">
                <i class="fas fa-trash-alt fa-2x"></i>
            </a>
                        ';
            })
            ->setRowId(function ($item) {
                return "row_" . $item->id;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function show(Request $request, $id)
    {
        $user = User::find($id);
        $superAdmin = User::where('role_id', User::ROLE_ADMIN)->first();
        if (!$user) {
            return errorResponse('User not Found.');
        }
        $fetchHLUsers = CRM::crmV2($superAdmin->id, 'users?locationId=' . $request->location_id, 'get', '', [], true, $request->location_id);
        if (is_string($fetchHLUsers)) {
            $fetchHLUsers = json_decode($fetchHLUsers, true);
        }
        if ($fetchHLUsers && property_exists($fetchHLUsers, 'users')) {
            $data = [
                'user' => $user,
                'crmUser' => $fetchHLUsers
            ];
            return successResponse($data);
        }
        return errorResponse('Invalid JWT');
    }
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return errorResponse('User not Found.');
        }
        try {
            $user->delete();
            return successResponse(null);
        } catch (\Exception $e) {
            return errorResponse('Failed to delete user.');
        }
    }
}
