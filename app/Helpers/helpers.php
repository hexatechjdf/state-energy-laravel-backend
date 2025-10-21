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
if (!function_exists('ghl_oauth_call')) {

    function ghl_oauth_call($code = '', $method = '', $type = 'Company')
    {

        $url = 'https://services.leadconnectorhq.com/oauth/token';
        $curl = curl_init();
        $data = [];
        $data['client_id'] =  env('CRM_CLIENT_ID');
        $data['client_secret'] = env('CRM_CLIENT_SECRET');

        $md = empty($method) ? 'code' : 'refresh_token';
        $data[$md] = $code;
        if (empty($code)) {
            return '';
        }
        $data['grant_type'] = empty($method) ? 'authorization_code' : 'refresh_token';

        $postv = '';
        $x = 0;
        foreach ($data as $key => $value) {
            if ($x > 0) {
                $postv .= '&';
            }
            $postv .= $key . '=' . $value;
            $x++;
        }

        $curlfields = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postv,
        );

        curl_setopt_array($curl, $curlfields);

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}
