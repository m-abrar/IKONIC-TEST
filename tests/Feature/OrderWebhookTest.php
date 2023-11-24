<?php
//Just completed this test
namespace Tests\Feature;

use App\Services\AffiliateService;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderWebhookTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_process_order()
    {
        $data = [
            'order_id' => $this->faker->uuid(),
            'subtotal_price' => round(rand(100, 999) / 3, 2),
            'merchant_domain' => $this->faker->domainName(),
            'discount_code' => $this->faker->uuid()
        ];

        $this->mock(OrderService::class)
            ->shouldReceive('processOrder')
            ->with($data)
            ->once();

        $this->post(route('webhook'), $data)->assertOk();
        //Just testing, but we are not supposed to make any changes in this file
        // $this->post(route('webhook'), $data + ['_token' => csrf_token()])->assertOk(); 
    }
}
