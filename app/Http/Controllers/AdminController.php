<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AdminController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'nullable|min:6',
        ]);

        try {
            $user = loginUser();

            // Update user details
            $user->name = $request->username;
            $user->email = $request->email;

            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
            }
            $user->save();
            $message = !empty($request->password) ? 'User profile and password updated successfully' : 'User profile updated successfully';
            return response()->json(['status' => 'Success', 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'Error', 'message' => $e->getMessage()], 500);
        }
    }
    public function dashboard(Request $request)
    {
        $user = loginUser();
        return view('location.dashboard',compact('user'));
    }
    public function index()
    {
        return view('admin.user.index');
    }
     public function getTableData(Request $req)
    {
        $items = User::where('role_id',User::ROLE_LOCATION)->whereNotNull('location_id');

        // Apply search filtering for specific columns
        if (!empty($req->search['value'])) {
            $searchValue = $req->search['value'];
            $items = $items->where(function ($query) use ($searchValue) {
                $query->where('location_id', 'like', "%{$searchValue}%")
                    ->orWhere('first_name', 'like', "%{$searchValue}%")
                    ->orWhere('last_name', 'like', "%{$searchValue}%");
            });
        }
        return DataTables::eloquent($items)
            ->editColumn('action', function ($item) {
                $keysToFetch = ['hpp_tpn', 'hpp_auth_token', 'environment'];
                $settings = $item->getSpecificSettings($keysToFetch);
                $encodedSettings = base64_encode(json_encode($settings));
                return '<a href="javascript:void(0);" class="text-warning btn-add-hpp" data-id="' . $item->id . '" data-location-id="' . $item->location_id . '" data-user-id="' . $item->user_id . '">
                            <i class="fas fa-cog fa-2x"></i>
                        </a>
                        ';
            })
            ->setRowId(function ($item) {
                return "row_" . $item->id;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
