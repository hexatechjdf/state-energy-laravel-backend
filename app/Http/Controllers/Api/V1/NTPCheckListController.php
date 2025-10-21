<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\CRM;
use App\Http\Controllers\Controller;
use App\Http\Resources\LenderResource;
use App\Models\ChecklistUpload;
use App\Models\Lender;
use App\Models\User;
use Illuminate\Http\Request;

class NTPCheckListController extends Controller
{
    public function store(Request $request)
    {
        $user = loginUser();
        $superAdmin  = User::where('role_id', User::ROLE_ADMIN)->first();
        $location_id = getSettingValue($superAdmin->id, 'location_id', '');

        $appointmentId = $request->appointment_id;
        $orderId       = $request->order_id;

        $categories = (array) $request->categories;

        $allResponses = [];

        foreach ($categories as $category) {
            $category = strtolower($category);
            $rules = config("checklists.$category");

            if (!$rules) {
                continue;
            }

            $validated = $request->validate($rules);

            foreach ($validated as $field => $value) {
                if (is_array($value)) {
                    foreach ($value as $file) {
                        $payload = [
                            'file' => $file,
                            'hosted' => false,
                            'name' => $file->getClientOriginalName(),
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
                            //throw $th;
                        }


                        ChecklistUpload::create([
                            'appointment_id' => $appointmentId,
                            'order_id'       => $orderId,
                            'category'       => $category,
                            'field_name'     => $field,
                            'file_name'      => $file->getClientOriginalName(),
                            'crm_file_id'    => $uploadFile['id'] ?? null,
                            'crm_response'   => $uploadFile,
                        ]);

                        $allResponses[$category][$field][] = $uploadFile;
                    }
                } else {
                    $file = $value;
                    $payload = [
                        'file' => $file,
                        'hosted' => false,
                        'name' => $file->getClientOriginalName(),
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
                        //throw $th;
                    }


                    ChecklistUpload::create([
                        'appointment_id' => $appointmentId,
                        'order_id'       => $orderId,
                        'category'       => $category,
                        'field_name'     => $field,
                        'file_name'      => $file->getClientOriginalName(),
                        'crm_file_id'    => $uploadFile['id'] ?? null,
                        'crm_response'   => $uploadFile,
                    ]);

                    $allResponses[$category][$field] = $uploadFile;
                }
            }
        }

        return response()->json([
            'status' => true,
            'crm_responses' => $allResponses
        ]);
    }
}
