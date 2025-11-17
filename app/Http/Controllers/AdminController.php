<?php

namespace App\Http\Controllers;

use App\Helpers\CRM;
use App\Http\Requests\Api\V1\UserStoreRequest;
use App\Http\Resources\UserResource;
use App\Jobs\SendGhlWelcomeEmail;
use App\Models\CrmToken;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

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
    public function connectCrmByType($type, $id)
    {

        if (empty($id)) {
            return "";
        }
        return view('admin.crm.oauth', get_defined_vars());
    }
    public function oAuthCallback(Request $request, $provider)
    {
        if ($provider !== 'crm') {
            abort(404, 'Provider not found.');
        }

        // 1. Validate the incoming request from the CRM
        $authorizationCode = $request->input('code');
        if (empty($authorizationCode)) {
            // This error is for the final fetch call from our JavaScript
            if ($request->has('onlyjson')) {
                return response()->json(['error' => 'Authorization code is missing.'], 400);
            }
            // This is for a user being redirected directly without a code
            return $this->handleError('Authorization code is required to connect.');
        }

        try {
            $tokenResponse = $this->exchangeCodeForToken($authorizationCode);
        } catch (\Exception $e) {
            // Log the detailed error for debugging
            \Log::error('CRM OAuth Callback Failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);


            return $this->handleError('An internal error occurred. Please try again later.');
        }
        try {
            $this->storeCrmToken($tokenResponse);
        } catch (\Exception $e) {
            // Log the detailed error for debugging
            \Log::error('CRM OAuth Callback Failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);


            return $this->handleError('An internal error occurred. Please try again later.');
        }
        return redirect()->route('admin.setting')->with('success', 'Connected successfully');
    }

    /**
     * Exchanges the authorization code for an access token.
     *
     * @param string $code
     * @return object
     * @throws \Exception
     */
    private function exchangeCodeForToken(string $code): object
    {
        $payload = [
            'client_id'     => env('CRM_CLIENT_ID'),
            'client_secret' => env('CRM_CLIENT_SECRET'),
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'user_type'     => 'Location',
            // 'redirect_uri'  => env('CRM_OAUTH_CALLBACK_URL_LOCATION'),
        ];
        $response = Http::asForm()->post('https://services.leadconnectorhq.com/oauth/token', $payload);
        if ($response->failed()) {
            throw new \Exception('Failed to obtain access token from provider. Status: ' . $response->status());
        }

        $tokenData = $response->object();
        if (isset($tokenData->error)) {
            throw new \Exception('Token provider returned an error: ' . ($tokenData->error_description ?? 'Unknown error'));
        }
        return $tokenData;
    }

    /**
     * Creates or updates the CRM token in the database.
     *
     * @param object $tokenData The response object from the token exchange.
     * @return void
     */
    private function storeCrmToken(object $tokenData): void
    {
        // Use updateOrCreate to either create a new record or update an existing one for the location.
        CrmToken::updateOrCreate(
            ['location_id' => $tokenData->locationId], // Match by location_id
            [
                'access_token'  => $tokenData->access_token,
                'refresh_token' => $tokenData->refresh_token,
                'expires_in'    => $tokenData->expires_in,
                'scope'         => $tokenData->scope,
                'user_type'     => $tokenData->userType,
                'company_id'    => $tokenData->companyId ?? null,
            ]
        );
        Setting::where('key', 'location_id')
            ->where('user_id', auth()->id())
            ->update(['value' => $tokenData->locationId]);
    }

    /**
     * A simple helper to show an error view.
     *
     * @param string $message
     * @return \Illuminate\View\View
     */
    private function handleError(string $message)
    {
        return redirect()->route('admin.setting')->with('error', $message);
    }
}
