<?php

namespace App\Jobs;

use App\Helpers\CRM;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendGhlWelcomeEmail implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected User $user;
    protected string $plainPassword;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $plainPassword)
    {
        $this->user = $user;
        $this->plainPassword = $plainPassword;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $superAdmin = User::where('role_id', User::ROLE_ADMIN)->first();
        if (!$superAdmin) {
            \Log::error('Super Admin not found. Cannot send welcome email.');
            return;
        }

        $locationId = getSettingValue($superAdmin->id, 'location_id', '');
        $contactPayload = [
            'firstName'   => $this->user->first_name,
            'lastName'    => $this->user->last_name,
            'name'        => $this->user->first_name . ' ' . $this->user->last_name,
            'email'       => $this->user->email,
            'phone'       => '+' . $this->user->dial_code . $this->user->phone,
            'locationId'  => $locationId,
            'address1'    => $this->user->address,
            'city'        => $this->user->city,
            'postalCode'  => $this->user->zip_code,
            'country'     => $this->user->country
        ];

        $contactResponse = CRM::crmV2(
            $superAdmin->id,
            'contacts/upsert?locationId=' . $locationId,
            'Post',
            $contactPayload,
            [],
            true,
            $locationId
        );

        if (is_string($contactResponse)) {
            $contactResponse = json_decode($contactResponse);
        }

        if ($contactResponse && property_exists($contactResponse, 'contact')) {
            $contactId = $contactResponse->contact->id;

            // Save contact_id in user record
            $this->user->update(['contact_id' => $contactId]);

            $loginUrl = config('app.url') . '/login';

            $emailPayload = [
                "type"      => "Email",
                "contactId" => $contactId,
                "emailTo"   => $this->user->email,
                "subject"   => "Your State Energy Login Details",
                "html"      => "
                    <p>Welcome to State Energy!</p>
                    <p>Your login credentials:</p>
                    <ul>
                        <li><strong>Email:</strong> {$this->user->email}</li>
                        <li><strong>Password:</strong> {$this->plainPassword}</li>
                    </ul>
                    <p><a href='{$loginUrl}'>Click here to login</a></p>
                ",
                "message"   => "Welcome to State Energy! Your login credentials are inside this message.",
                "locationId" => $locationId,
            ];

            $messageResponse = CRM::crmV2(
                $superAdmin->id,
                'conversations/messages?locationId=' . $locationId,
                'Post',
                $emailPayload,
                [],
                true,
                $locationId
            );

            if (is_string($messageResponse)) {
                $messageResponse = json_decode($messageResponse);
            }

            if (!property_exists($messageResponse, 'id')) {
                \Log::error('Failed to send welcome email via GHL.', ['response' => $messageResponse]);
            }
        } else {
            \Log::error('Failed to create or fetch contact from GHL.', ['response' => $contactResponse]);
        }
    }
}
