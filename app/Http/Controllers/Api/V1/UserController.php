<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\CRM;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PasswordChangeRequest;
use App\Http\Requests\Api\V1\UserStoreRequest;
use App\Http\Requests\Api\V1\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\Order;
use App\Models\Setting;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\SendDispositionRequest;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);
        return successResponse(UserResource::collection($users));
    }

    public function store(UserStoreRequest $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return successResponse(new UserResource($user), Response::HTTP_CREATED);
    }

    public function show(User $user)
    {
        return successResponse(new UserResource($user));
    }

    public function update(UserUpdateRequest $request, User $user, FileUploadService $fileService)
    {
        $validated = $request->validated();
        if ($request->hasFile('avatar')) {
            // Optional: delete old avatar file if exists
            if ($user->avatar) {
                $fileService->delete($user->avatar, 'public');
            }

            // Upload new avatar
            $avatarPath = $fileService->upload($request->file('avatar'), 'avatars', 'public');

            // Add avatar path to validated data
            $validated['avatar'] = $avatarPath;
        }

        // Update user record
        $user->update($validated);
        return successResponse(new UserResource($user->refresh()));
    }

    public function destroy(User $user)
    {
        $user->delete();
        return successResponse('User deleted successfully');
    }
    public function getAppointment(Request $request)
    {
        $user = User::where('role_id', User::ROLE_ADMIN)->first();
        $filter = $request->get('filter', '');
        $customStart = $request->get('start');
        $customEnd = $request->get('end');
        $loggedInUser = auth()->user();
        $location_id = getSettingValue($user->id, 'location_id', '');
        $range = getDateRangeByFilter($filter, $customStart, $customEnd);
        $fetchCalendarEvent = CRM::crmV2(
            $user->id,
            'calendars/events?locationId=' . $location_id .
                '&startTime=' . $range['start'] .
                '&endTime=' . $range['end'] .
                '&userId=' . $loggedInUser->user_id,
            'get',
            '',
            [],
            true,
            $location_id
        );
        if (is_string($fetchCalendarEvent)) {
            $fetchCalendarEvent = json_decode($fetchCalendarEvent, true);
        }

        if ($fetchCalendarEvent && property_exists($fetchCalendarEvent, 'events')) {
            return successResponse($fetchCalendarEvent);
        }
        return errorResponse('Invalid JWT');
    }
    public function getHLUsers(Request $request)
    {
        $user = User::where('role_id', User::ROLE_ADMIN)->first();
        $locationId = $request->location_id ?? null;
        if (!isset($locationId))
            $locationId = getSettingValue($user->id, 'location_id', null);
        if (!$user) {
            return errorResponse('Invalid User');
        }
        $fetchHLUsers = CRM::crmV2($user->id, 'users?locationId=' . $locationId, 'get', '', [], true, $locationId);
        if (is_string($fetchHLUsers)) {
            $fetchHLUsers = json_decode($fetchHLUsers, true);
        }

        if ($fetchHLUsers && property_exists($fetchHLUsers, 'users')) {
            return successResponse($fetchHLUsers);
        }
        return errorResponse('Invalid JWT');
    }
    public function getCRMContact(Request $request)
    {
        $user = User::where('role_id', User::ROLE_ADMIN)->first();
        $locationId = getSettingValue($user->id, 'location_id', '');
        if (!$user) {
            return errorResponse('Invalid User');
        }
        $fetchHLContact = CRM::crmV2($user->id, 'contacts/' . $request->contact_id . '?locationId=' . $locationId, 'get', '', [], true, $locationId);
        if (is_string($fetchHLContact)) {
            $fetchHLContact = json_decode($fetchHLContact, true);
        }

        if ($fetchHLContact && property_exists($fetchHLContact, 'contact')) {
            return successResponse($fetchHLContact);
        }
        return errorResponse('Invalid JWT');
    }
    public function changePassword(PasswordChangeRequest $request)
    {
        $user = $request->user();

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return errorResponse('The current password is incorrect.');
        }

        // Update to new password
        $user->password = Hash::make($request->new_password);
        if ($request->filled('email') && $request->email !== $user->email) {
            $user->email = $request->email;
        }
        $user->save();
        return successResponse(new UserResource($user->refresh()));
    }
    public function sendDisposition(SendDispositionRequest $request)
    {
        $user = loginUser();

        $superAdmin = User::where('role_id', User::ROLE_ADMIN)->first();

        if (!$superAdmin) {
            return errorResponse('Super Admin not found.', 404);
        }

        $disposition_webhook_url = getSettingValue($superAdmin->id, 'disposition_webhook_url', '');

        if (empty($disposition_webhook_url)) {
            return errorResponse('Disposition webhook URL not configured.', 400);
        }

        $payload = [
            'appointment_id'  => $request->appointment_id,
            'contact_id'      => $request->contact_id,
            'dispo'           => $request->disposition,
            'dispo_note'      => $request->disposition_note,
        ];

        if (strtolower($request->disposition) === 'sale' ) {
            $order = Order::where('user_id', $user->id)
                ->where('appointment_id', $request->appointment_id)
                ->first();

            $payload['sale_amount'] = $order->total_amount ?? 0.00;
        }
        Http::post($disposition_webhook_url, $payload);
        return successResponse(['message' => 'Disposition sent successfully.']);
    }
}
