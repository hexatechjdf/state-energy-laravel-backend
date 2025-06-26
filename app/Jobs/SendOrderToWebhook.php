<?php

namespace App\Jobs;

use App\Http\Resources\Api\V1\OrderResource;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendOrderToWebhook implements ShouldQueue
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
        $user = User::where('role_id',User::ROLE_ADMIN)->first();
        $webhookUrl = getSettingValue($user->id, 'order_webhook_url', '');


        if (!$webhookUrl) {
            Log::warning("No webhook URL configured for user ID {$this->order->user_id}");
            return;
        }
       $payload = (new OrderResource($this->order))->toArray(request());

        // Send to webhook
        Http::post($webhookUrl, $payload);
    }
}
