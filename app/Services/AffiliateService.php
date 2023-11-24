<?php

namespace App\Services;

use App\Exceptions\AffiliateCreateException;
use App\Mail\AffiliateCreated;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AffiliateService
{
    public function __construct(
        protected ApiService $apiService
    ) {}

    /**
     * Create a new affiliate for the merchant with the given commission rate.
     *
     * @param  Merchant $merchant
     * @param  string $email
     * @param  string $name
     * @param  float $commissionRate
     * @return Affiliate
     */
    public function register(Merchant $merchant, string $email, string $name, float $commissionRate): Affiliate
    {
        // TODO: Complete this method //DONE

        // Check if the email is already associated with the merchant user
        if ($merchant->user->email === $email) {
            throw new AffiliateCreateException('Email is already associated with the merchant.');
        }

        // Check if the email is already associated with an existing affiliate for the same merchant
        $existingAffiliate = Affiliate::whereHas('merchant', function ($query) use ($merchant) {
            $query->where('id', $merchant->id);
        })->whereHas('user', function ($query) use ($email) {
            $query->where('email', $email);
        })->exists();

        if ($existingAffiliate) {
            throw new AffiliateCreateException('Email is already associated with another affiliate for the same merchant.');
        }

        // Create a discount code
        $discountCodeData = $this->apiService->createDiscountCode();

        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $name, 'type' => 'default']
        );
        // New affiliate
        $affiliate = Affiliate::create([
            'merchant_id' => $merchant->id,
            'user_id' => $user->id,
            'commission_rate' => $commissionRate,
            'discount_code' => $discountCodeData['code'],
        ]);

        // Send email
        Mail::to($email)->send(new AffiliateCreated($affiliate));

        return $affiliate;
        
    }

    /**
     * Checks if the given email is associated with the provided affiliate.
     *
     * @param  Affiliate $affiliate
     * @param  string $email
     * @return bool
     */
    public function isCustomerAssociated(Affiliate $affiliate, string $email): bool
    {
        return $affiliate->user->email === $email;
    }
}
