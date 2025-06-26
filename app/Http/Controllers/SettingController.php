<?php

namespace App\Http\Controllers;

use App\Helpers\CRM;
use App\Models\Setting;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = loginUser();
        $settings = Setting::where('user_id', $user->id)->pluck('value', 'key')->toArray();
        $locationId = $settings['location_id'];
        $emailTemplatesList = CRM::crmV2(
            $user->id,
            'emails/builder?limit=100&locationId=' . $locationId,
            'get',
            '',
            [],
            true,
            $locationId
        );
        return view('setting.index', compact('settings', 'user', 'emailTemplatesList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = loginUser();
        foreach ($request->setting ?? [] as $key => $value) {
            save_settings($key, $value, $user->id);
        }
        return response()->json(['success' => true, 'message' => 'Data saved successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function edit(Setting $setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Setting $setting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Setting $setting)
    {
        //
    }
    private function evp_bytes_to_key($password, $salt)
    {
        $key = '';
        $iv = '';
        $derived_bytes = '';
        $previous = '';

        // Concatenate MD5 results until we generate enough key material (32 bytes key + 16 bytes IV = 48 bytes)
        while (strlen($derived_bytes) < 48) {
            $previous = md5($previous . $password . $salt, true);
            $derived_bytes .= $previous;
        }

        // Split the derived bytes into the key (first 32 bytes) and IV (next 16 bytes)
        $key = substr($derived_bytes, 0, 32);
        $iv = substr($derived_bytes, 32, 16);

        return [
            $key,
            $iv
        ];
    }
    public function decryptSSO(Request $request)
    {
        try {
            $ssoKey = env('SSO_KEY', null);
            if (!$ssoKey) {
                return response()->json(['status' => false, 'message' => 'SSO key is not configured.']);
            }
            $ciphertext = base64_decode($request->ssoToken);

            if (substr($ciphertext, 0, 8) !== "Salted__") {
                return response()->json(['status' => false]);
            }
            $salt = substr($ciphertext, 8, 8);
            $ciphertext = substr($ciphertext, 16);
            list($key, $iv) = self::evp_bytes_to_key($ssoKey, $salt);
            $decrypted = openssl_decrypt($ciphertext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
            if ($decrypted === false) {
                return response()->json(['status' => false]);
            } else {
                $decrypted_data = json_decode($decrypted, true);
                $location_id = isset($decrypted_data['activeLocation']) ? $decrypted_data['activeLocation'] : null;
                $user = User::where('location_id', $location_id)
                    ->first();

                if (!$user) {
                    return response()->json(['status' => false, 'message' => "Location Does Not exist in the software Try uninstall and install the app again."]);
                }
                Auth::login($user);
                if (Auth::check()) {
                    return response()->json(['status' => true, 'user' => Auth::user()]);
                }
                return response()->json(['status' => false, 'message' => 'Auth session initialization failed.']);
            }
        } catch (Exception $e) {
            Log::error('SSO Decryption Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'An error occurred while processing your request.']);
        }
    }
    public function emailTemplateList(Request $request)
    {
        $user = loginUser();
        $settings = Setting::where('user_id', $user->id)->pluck('value', 'key')->toArray();
        $locationId = $settings['location_id'];

        $searchQuery = $request->input('q', '');
        $page = (int)$request->input('page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        // Build query string with search, limit, offset, and locationId
        $queryParams = http_build_query([
            'name'     => $searchQuery,
            'limit'      => $perPage,
            'offset'     => $offset,
            'locationId' => $locationId
        ]);

        // Call external API
        $emailTemplatesList = CRM::crmV2(
            $user->id,
            'emails/builder?' . $queryParams,
            'get',
            '',
            [],
            true,
            $locationId
        );

        // Extract builders array and total
        $builders = $emailTemplatesList->builders ?? [];
        $total = $emailTemplatesList->total[0]->total ?? 0;

        // Return formatted Select2 compatible JSON
        return response()->json([
            'data'          => $builders,
            'total'         => $total,
            'per_page'      => $perPage,
            'current_page'  => $page
        ]);
    }
}
