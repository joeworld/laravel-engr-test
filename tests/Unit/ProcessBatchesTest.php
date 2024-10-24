<?php

namespace Tests\Unit;

use App\Actions\ProcessBatches;
use App\Models\HMO;
use App\Models\Order;
use App\Models\Batch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class ProcessBatchesTest extends TestCase
{
    use RefreshDatabase;

    public function test_process_batches_creates_batches_based_on_criteria()
    {
        // Create an HMO with 'submission_date' batching criteria
        $hmo1 = HMO::create([
            'name' => 'HMO A',
            'code' => 'HMO123',
            'batching_criteria' => 'submission_date',
            'email' => 'contact@hmoa.com',
        ]);

        // Create an HMO with 'encounter_date' batching criteria
        $hmo2 = HMO::create([
            'name' => 'HMO B',
            'code' => 'HMO456',
            'batching_criteria' => 'encounter_date',
            'email' => 'contact@hmob.com',
        ]);

        // Create orders for both HMOs
        $order1 = Order::create([
            'provider_name' => 'Provider 1',
            'hmo_id' => $hmo1->id,
            'encounter_date' => '2024-10-10',
            'order_amount' => 100.00,
        ]);

        $order2 = Order::create([
            'provider_name' => 'Provider 2',
            'hmo_id' => $hmo2->id,
            'encounter_date' => '2024-09-15',
            'order_amount' => 200.00,
        ]);

        // Run the ProcessBatches action
        (new ProcessBatches())->handle();

        // Assert that batches were created for both orders
        $this->assertDatabaseCount('batches', 2);

        // Check the first batch's details for HMO with 'submission_date'
        $batch1 = Batch::where('provider_name', 'Provider 1')->first();
        $this->assertNotNull($batch1);
        $this->assertEquals('Provider 1 ' . Carbon::parse($order1->created_at)->format('M Y'), $batch1->batch_name);

        // Check the second batch's details for HMO with 'encounter_date'
        $batch2 = Batch::where('provider_name', 'Provider 2')->first();
        $this->assertNotNull($batch2);
        $this->assertEquals('Provider 2 ' . Carbon::parse($order2->encounter_date)->format('M Y'), $batch2->batch_name);

        // Assert that the orders are now associated with their respective batches
        $this->assertEquals($batch1->id, $order1->fresh()->batch_id);
        $this->assertEquals($batch2->id, $order2->fresh()->batch_id);
    }
}