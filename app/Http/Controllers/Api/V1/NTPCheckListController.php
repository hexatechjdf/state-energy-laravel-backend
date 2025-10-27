<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\CRM;
use App\Http\Controllers\Controller;
use App\Http\Resources\LenderResource;
use App\Models\ChecklistUpload;
use App\Models\Lender;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NTPCheckListController extends Controller
{
    public function store(Request $request)
    {
        $user = loginUser();
        $superAdmin = User::where('role_id', User::ROLE_ADMIN)->first();
        $location_id = getSettingValue($superAdmin->id, 'location_id', '');

        $appointmentId = $request->appointment_id;
        $orderId = $request->order_id;

        if (!$appointmentId || !$orderId) {
            return response()->json([
                'status'  => false,
                'message' => 'Appointment ID and Order ID are required.',
            ], 400);
        }

        if (!$request->has('categories')) {
            return response()->json([
                'status'  => false,
                'message' => 'Categories are required.',
            ], 400);
        }

        // Ensure categories is always an array
        $categories = $request->categories;
        if (is_string($categories)) {
            $categories = json_decode($categories, true);
        }

        if (!is_array($categories)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid categories format.',
            ], 400);
        }

        $allResponses = [];

        foreach ($categories as $category) {
            $category_key = strtolower($category['category_key'] ?? '');
            $category_id = strtolower($category['category_id'] ?? '');

            if (!$category_key || !$category_id) {
                continue;
            }

            $rules = config("checklists.$category_key");
            if (!$rules) {
                continue;
            }

            $validated = $request->validate($rules);

            foreach ($validated as $field => $value) {
                $files = is_array($value) ? $value : [$value];

                foreach ($files as $file) {
                    if (!$file instanceof \Illuminate\Http\UploadedFile) {
                        continue;
                    }
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $customName = "{$originalName}_appointment_{$appointmentId}_order_{$orderId}.{$extension}";
                    $payload = [
                        'file' => new \CURLFile(
                            $file->getRealPath(),
                            $file->getMimeType(),
                            $customName
                        ),
                        'hosted' => false,
                        'name' => $customName,
                    ];

                    try {
                        $uploadFile = CRM::crmV2(
                            $superAdmin->id ?? 0,
                            'medias/upload-file?locationId=' . $location_id,
                            'post',
                            $payload,
                            [],
                            true,
                            $location_id
                        );
                    } catch (\Throwable $th) {
                        Log::error('CRM V2 Upload Error', [
                            'message' => $th->getMessage(),
                            'file' => $th->getFile(),
                            'line' => $th->getLine(),
                            'trace' => $th->getTraceAsString(),
                        ]);

                        $uploadFile = null;
                    }

                    ChecklistUpload::create([
                        'appointment_id' => $appointmentId,
                        'order_id'       => $orderId,
                        'category_id'    => $category_id,
                        'field_name'     => $field,
                        'file_name'      => $file->getClientOriginalName(),
                        'crm_file_id'    => $uploadFile->id ?? null,
                        'crm_response'   => $uploadFile,
                    ]);

                    $allResponses[$category_key][$field][] = [
                        'file_name'    => $file->getClientOriginalName(),
                        'crm_file_id'  => $uploadFile->id ?? null,
                        'crm_response' => $uploadFile,
                    ];
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Files uploaded successfully.',
            'crm_responses' => $allResponses,
        ]);
    }
}
