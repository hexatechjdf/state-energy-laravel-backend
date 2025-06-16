<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'settings'   => 'required|array',
            'settings.*.key'   => 'required|string',
            'settings.*.value' => 'nullable|string',
        ]);

        $userId = $request->user_id;

        foreach ($request->settings as $setting) {
            Setting::updateOrCreate(
                [
                    'user_id' => $userId,
                    'key'     => $setting['key']
                ],
                [
                    'value'   => $setting['value']
                ]
            );
        }
        return successResponse([
            'result'  => null,
        ]);
    }

    public function index(Request $request)
    {
        $settings = Setting::where('user_id',$request->user_id)->get(['key', 'value']);

         return successResponse([
            'settings'  => $settings,
        ]);
    }
}
