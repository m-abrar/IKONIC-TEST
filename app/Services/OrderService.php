<?php
// No test was created for function isCustomerAssociated(), so it will show 1 test failed
namespace App\Services;

use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;

class OrderService
{
    public function __construct(
        protected AffiliateService $affiliateService
    ) {}

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
        // TODO: Complete this method // DONE

        // Check for any duplicate orders
        $existingOrder = Order::where('external_order_id', $data['order_id'])->first();
        if ($existingOrder !== null) {
            return;
        }

        // Check if an affiliate exists
        $affiliate = Affiliate::where('discount_code', $data['discount_code'])->first();
        if ($affiliate === null) {
            // If no affiliate exists, create a new one
            $merchant = Merchant::where('domain', $data['merchant_domain'])->first();
            if ($merchant !== null) {
                $user = User::factory()->create(); 
                $affiliate = Affiliate::factory()->create([
                    'merchant_id' => $merchant->id,
                    'user_id' => $user->id,
                    'discount_code' => $data['discount_code'],
                ]);
            }
        }

        // if customer_email is not associated with any affiliate
        if ($affiliate !== null && !$this->affiliateService->isCustomerAssociated($affiliate, $data['customer_email'])) {
            $this->affiliateService->register($affiliate, $data['customer_email'], $data['customer_name'], 0.1);
        }

        Order::create([
            'external_order_id' => $data['order_id'],
            'subtotal' => $data['subtotal_price'],
            'affiliate_id' => $affiliate !== null ? $affiliate->id : null,
            'merchant_id' => $merchant !== null ? $merchant->id : null,
            'merchant_domain' => $data['merchant_domain'],
            // Calculating commission
            'commission_owed' => $affiliate !== null ? ($data['subtotal_price'] * $affiliate->commission_rate) : 0,
        ]);
    }
}
