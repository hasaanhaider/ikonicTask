<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\ApiService;
use Carbon\Exceptions\Exception;
use Exception as GlobalException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class PayoutOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public Order $order
    ) {}

    /**
     * Use the API service to send a payout of the correct amount.
     * Note: The order status must be paid if the payout is successful, or remain unpaid in the event of an exception.
     *
     * @return void
     */
    public function handle(ApiService $apiService)
    {
        // TODO: Complete this method

        try {
            $payoutResult = $apiService->sendPayout('email',$this->order->amount);
            if ($payoutResult['success']) {
                $this->order->STATUS_PAID;
            } else {
                $errorMessage = $payoutResult['error_message'];
                \Log::error("Payout failed for order #{$this->order->id}: $errorMessage");
            }
        } catch (GlobalException $e) {
            \Log::error("Exception while processing payout for order #{$this->order->id}: " . $e->getMessage());
        }
    }
}
