<?php

namespace App\Jobs;

use App\Helpers\CRM;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

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
     *
     * @return array  List of upserted CRM contact IDs
     */
    public function handle(): array
    {
        $superAdmin  = User::where('role_id', User::ROLE_ADMIN)->first();
        $location_id = getSettingValue($superAdmin->id, 'location_id', '');
        $contactIds = [];
        if (!is_array($this->order->contacts)) {
            $contact = [
                'first_name'   => $this->order->first_name,
                'last_name'    => $this->order->last_name,
                'email'        => $this->order->email,
                'phone_number' => $this->order->phone_number,
                'address'      => $this->order->address,
                'city'         => $this->order->city,
                'zip_code'     => $this->order->zip_code,
            ];
            $contactIds[] = $this->upsertContact($contact, $superAdmin->id, $location_id);
        } else {
            foreach ($this->order->contacts as $contact) {
                $id = $this->upsertContact($contact, $superAdmin->id, $location_id);
                if ($id) {
                    $contactIds[] = $id;
                }
            }
        }
        $order = Order::find($this->order->id);
        if ($order) {
            $order->contact_ids = json_encode($contactIds);
            $order->save();
        }
        return array_filter($contactIds);
    }

    /**
     * Handle CRM upsert for a single contact.
     *
     * @return string|null  CRM contact ID
     */
    protected function upsertContact(array $contact, int $adminId, string $location_id): ?string
    {
        $payload = [
            'firstName'   => $contact['first_name'] ?? '',
            'lastName'    => $contact['last_name'] ?? '',
            'name'        => trim(($contact['first_name'] ?? '') . ' ' . ($contact['last_name'] ?? '')),
            'email'       => $contact['email'] ?? '',
            'locationId'  => $location_id,
            'phone'       => $contact['phone_number'] ?? '',
            'address1'    => $contact['address'] ?? '',
            'city'        => $contact['city'] ?? '',
            'postalCode'  => $contact['zip_code'] ?? '',
        ];

        $upsertContact = CRM::crmV2(
            $adminId,
            'contacts/upsert?locationId=' . $location_id,
            'post',
            $payload,
            [],
            true,
            $location_id
        );

        if (is_string($upsertContact)) {
            $upsertContact = json_decode($upsertContact, true);
        }
        return $upsertContact['contact']->id ?? null;
    }
}
