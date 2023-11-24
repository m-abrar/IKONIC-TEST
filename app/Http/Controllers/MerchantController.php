<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Services\MerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Order;

class MerchantController extends Controller
{
    public function __construct(
        MerchantService $merchantService
    ) {}

    /**
     * Useful order statistics for the merchant API.
     * 
     * @param Request $request Will include a from and to date
     * @return JsonResponse Should be in the form {count: total number of orders in range, commission_owed: amount of unpaid commissions for orders with an affiliate, revenue: sum order subtotals}
     */
    public function orderStats(Request $request): JsonResponse
    {
        // TODO: Complete this method // DONE
        $from = $request->input('from', now()->subDay());
        // echo $from . '<br/>'; //debug
        $to = $request->input('to', now());
        // echo $to . '<br/>'; //debug
        // echo now() . '<br/>'; //debug
        $merchant = Merchant::where('user_id', auth()->id())->firstOrFail();
        // dd($merchant);
        $orders = Order::where('merchant_id', $merchant->id)
            ->with('affiliate.user')
            ->whereBetween('created_at', [$from, $to])
            ->get();
        // dd($orders); //debug


        $orderCount = $orders->count();
        // echo $orderCount .'<br/>'; //debug
        $revenue = $orders->sum('subtotal');

        $noAffiliateCommission = $orders->filter(function ($order) {
            return $order->affiliate_id === null;
        })->sum('commission_owed');
        // echo $noAffiliateCommission . "<br/>"; //debug
        $orders->sum('commission_owed');
        $commissionsOwed = $orders->sum('commission_owed') - $noAffiliateCommission;

        return response()->json([
            'count' => $orderCount,
            'revenue' => $revenue,
            'commissions_owed' => $commissionsOwed,
        ]);
    }
}
