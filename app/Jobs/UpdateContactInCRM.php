<?php

namespace App\Jobs;

use App\Helpers\CRM;
use App\Http\Resources\Api\V1\OrderResource;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateContactInCRM implements ShouldQueue
{
    use Queueable;
    protected $order;
    /**
     * Create a new job instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Get webhook URL from settings
        $superAdmin = User::where('role_id',User::ROLE_ADMIN)->first();
        $location_id = getSettingValue($superAdmin->id, 'location_id', '');
        $payload = [
            'firstName' => $this->order->first_name,
            'lastName' => $this->order->last_name,
            'name' => $this->order->first_name .  $this->order->last_name,
            'email' => $this->order->email,
            'locationId' => $location_id,
            'phone' => $this->order->phone_number,
            'address1' => $this->order->address,
            'city' => $this->order->city,
            'postalCode' => $this->order->zip_code,
        ];
        $upsertContact = CRM::crmV2($superAdmin->id, 'contacts/upsert?locationId=' . $location_id, 'post', '', [], true, $location_id);

        if (is_string($upsertContact)) {
            $upsertContact = json_decode($upsertContact, true);
        }
        if ($upsertContact && property_exists($upsertContact, 'contact')) {
            $this->order->contact_id=$upsertContact->contact->id;
            $this->order->save();
        }
    }
}
