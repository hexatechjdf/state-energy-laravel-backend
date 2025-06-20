<?php

use App\Helpers\gCache;
use App\Models\Setting;
use Symfony\Component\HttpFoundation\Response;

function getSettingValue($user_id, $key, $def = '')
{
    return Setting::where('user_id', $user_id)
                  ->where('key', $key)
                  ->value('value') ?? $def;
}

if (!function_exists('successResponse')) {
    function successResponse($data, $status = Response::HTTP_OK)
    {
        return response()->json([
            'success' => true,
            'data'    => $data
        ], $status);
    }
}

if (!function_exists('errorResponse')) {
    function errorResponse($message, $status = Response::HTTP_BAD_REQUEST)
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $status);
    }
}
function loginUser()
{
    return auth()->user();
}
function save_settings($key, $value = '', $user_id = null)
{
    $setting = Setting::updateOrCreate(
        ['key' => $key, 'user_id' => $user_id,],
        [
            'value' => $value,
            'user_id' => $user_id,
            'key' => $key,
        ]
    );
    $cacheKey = 'setting_' . $user_id . '_' . $key;
    gCache::put($cacheKey, $value);
    return $setting;
}
