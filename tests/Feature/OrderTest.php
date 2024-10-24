<?php

namespace Tests\Feature;

use App\Models\Hmo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_the_create_order_page()
    {
        $response = $this->get(route('orders.create'));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertInertia(fn ($page) => $page->component('SubmitOrder'));
    }

    public function test_submits_an_order_successfully()
    {
        $this->newHmo();

        $orderData = [
            'code' => 'HMO123',
            'provider_name' => 'Provider Name',
            'encounter_date' => '2024-10-23',
            'items' => [
                ['name' => 'Item 1', 'unit_price' => 10.00, 'quantity' => 2],
            ],
        ];

        // Simulating the form submission
        $response = $this->post(route('orders.store'), $orderData);

        // Check if the session has the success message
        $this->assertTrue(Session::has('success_message'), 'Session does not have success message.');
        $response->assertSessionHas('success_message', 'Order submitted successfully.');

        // Check the redirection
        $response->assertRedirect(route('orders.create'));
    }

    public function test_validates_order_items()
    {
        $this->newHmo();

        $orderData = [
            'code' => 'HMO123',
            'provider_name' => 'Provider Name',
            'encounter_date' => '2024-10-23',
            'items' => [
                ['name' => null, 'unit_price' => 10.00, 'quantity' => 2],
                ['name' => 'Item 2', 'unit_price' => -5, 'quantity' => 2],
                ['name' => 'Item 3', 'unit_price' => 10.00, 'quantity' => -1],
            ],
        ];

        $response = $this->post(route('orders.store'), $orderData);

        $response->assertSessionHasErrors([
            'items.0.name' => 'Each order item must have a name.',
            'items.1.unit_price' => 'The items.1.unit_price field must be at least 0.',
            'items.2.quantity' => 'The items.2.quantity field must be at least 1.',
        ]);
    }

    public function test_fails_with_invalid_hmo_code()
    {
        $orderData = [
            'code' => 'INVALID_CODE',
            'provider_name' => 'Provider Name',
            'encounter_date' => '2024-10-23',
            'items' => [
                ['name' => 'Item 1', 'unit_price' => 10.00, 'quantity' => 2],
            ],
        ];

        $response = $this->post(route('orders.store'), $orderData);

        $response->assertSessionHasErrors(['code']);
        $this->assertEquals(session('errors')->first('code'), 'The specified HMO does not exist.');
    }

    public function test_stores_order_data_in_database()
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
                ['name' => 'Item 1', 'unit_price' => 10.00, 'quantity' => 2],
            ],
        ];

        $this->post(route('orders.store'), $orderData);

        $this->assertDatabaseHas('orders', [
            'provider_name' => 'Provider Name',
            'encounter_date' => '2024-10-23 00:00:00',
        ]);

        $this->assertDatabaseHas('order_items', [
            'item_name' => 'Item 1',
            'unit_price' => 10.00,
            'quantity' => 2,
        ]);
    }

    public function test_validates_order_submission()
    {
        $response = $this->post(route('orders.store'), []); // Sending empty data

        $response->assertSessionHasErrors(['code', 'provider_name', 'encounter_date', 'items']);
    }

    private function newHmo()
    {
        return Hmo::create([
            'name' => 'HMO A',
            'code' => 'HMO123',
            'batching_criteria' => 'submission_date',
            'email' => 'contact@hmoa.com',
        ]);
    }
}