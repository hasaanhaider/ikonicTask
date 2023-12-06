<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;

class OrderService
{
    public function __construct(
        protected AffiliateService $affiliateService,
        protected MerchantService $merchantService
    ) {
    }

    /**
     * Process an order and log any commissions.
     * This should create a new affiliate if the customer_email is not already associated with one.
     * This method should also ignore duplicates based on order_id.
     *
     * @param  array{order_id: string, subtotal_price: float, merchant_domain: string, discount_code: string, customer_email: string, customer_name: string} $data
     * @return void
     */
    public function processOrder(array $data)
    {
        // TODO: Complete this method

        if (Order::where('order_id', $data['order_id'])->exists()) {
            return;
        }

        $affiliate = Affiliate::where('email', $data['email'])->first();

        if (!$affiliate) {

            $merchant = $this->merchantService->findMerchantByEmail($data['email']);

            $affiliate = $this->affiliateService->register(
                $merchant,
                $merchant->email,
                $merchant->name,
                $data['commission_rate'],
            );
        }

        Order::create($data);
    }
}
