<?php

namespace Tests\Unit;

use App\Actions\SubmitOrder;
use App\Http\Requests\SubmitOrderRequest;
use App\Models\Hmo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmitOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_submit_order_successfully()
    {
        // Create an HMO record
        Hmo::create([
            'name' => 'HMO A',
            'code' => 'HMO123',
            'batching_criteria' => 'submission_date',
            'email' => 'contact@hmoa.com',
        ]);

        $orderData = [
            'code' => 'HMO123',
            'provider_name' => 'Provider Name',
            'encounter_date' => '2024-10-23',
            'items' => [
                ['name' => 'Item 1', 'unit_price' => 10.00, 'quantity' => 2],
                ['name' => 'Item 2', 'unit_price' => 20.00, 'quantity' => 1],
            ],
        ];

        // Create a SubmitOrderRequest instance with the order data
        $request = SubmitOrderRequest::create('/', 'POST', $orderData);

        // Run the SubmitOrder action
        $order = SubmitOrder::run($request);

        // Check that an order is created in the database
        $this->assertNotNull($order);
        $this->assertEquals('Provider Name', $order->provider_name);
        $this->assertEquals('2024-10-23 00:00:00', $order->encounter_date);
        $this->assertDatabaseHas('orders', ['provider_name' => 'Provider Name']);
    }

    public function test_fails_if_hmo_code_is_invalid()
    {
        $orderData = [
            'code' => 'INVALID_CODE', // Invalid HMO code
            'provider_name' => 'Provider Name',
            'encounter_date' => '2024-10-23',
            'items' => [
                ['name' => 'Item 1', 'unit_price' => 10.00, 'quantity' => 2],
            ],
        ];

        $request = SubmitOrderRequest::create('/', 'POST', $orderData);

        // Expecting the action to throw a model not found exception
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        // Run the action
        SubmitOrder::run($request);
    }

    public function test_validates_order_data_properly()
    {
        $orderData = [
            'code' => '', // Missing HMO code
            'provider_name' => '', // Missing provider name
            'encounter_date' => '', // Missing encounter date
            'items' => [], // Empty items
        ];

        $request = SubmitOrderRequest::create('/', 'POST', $orderData);

        // Validate the request to catch validation exceptions
        $validator = \Illuminate\Support\Facades\Validator::make($orderData, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('code', $validator->errors()->messages());
        $this->assertArrayHasKey('provider_name', $validator->errors()->messages());
        $this->assertArrayHasKey('encounter_date', $validator->errors()->messages());
        $this->assertArrayHasKey('items', $validator->errors()->messages());
    }

    public function test_handles_empty_items_gracefully()
    {
        Hmo::create([
            'name' => 'HMO A',
            'code' => 'HMO123',
            'batching_criteria' => 'submission_date',
            'email' => 'contact@hmoa.com',
        ]);

        $orderData = [
            'code' => 'HMO123',
            'provider_name' => 'Provider Name',
            'encounter_date' => '2024-10-23',
            'items' => [], // No items
        ];

        $request = SubmitOrderRequest::create('/', 'POST', $orderData);

        // Run the action
        $order = SubmitOrder::run($request);

        // Ensure the order is created without any items
        $this->assertNotNull($order);
        $this->assertEquals(0, $order->items()->count());
        $this->assertEquals(0, $order->order_amount);
    }

    public function test_calculates_total_amount_correctly()
    {
        Hmo::create([
            'name' => 'HMO A',
            'code' => 'HMO123',
            'batching_criteria' => 'submission_date',
            'email' => 'contact@hmoa.com',
        ]);

        $orderData = [
            'code' => 'HMO123',
            'provider_name' => 'Provider Name',
            'encounter_date' => '2024-10-23',
            'items' => [
                ['name' => 'Item 1', 'unit_price' => 10.00, 'quantity' => 2], // 20.00
                ['name' => 'Item 2', 'unit_price' => 15.50, 'quantity' => 1], // 15.50
                ['name' => 'Item 3', 'unit_price' => 7.25, 'quantity' => 3], // 21.75
            ],
        ];

        $request = SubmitOrderRequest::create('/', 'POST', $orderData);

        // Run the action
        $order = SubmitOrder::run($request);

        // Check if the total amount is calculated correctly (20.00 + 15.50 + 21.75 = 57.25)
        $this->assertEquals(57.25, $order->order_amount);
    }

}