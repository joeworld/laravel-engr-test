<?php

namespace App\Actions;

use App\Models\Order;
use App\Models\Batch;
use App\Models\HMO;
use Lorisleiva\Actions\Concerns\AsAction;
use Carbon\Carbon;

class ProcessBatches
{
    use AsAction;

    /**
     * Handle the batch processing logic.
     *
     * @return void
     */
    public function handle()
    {
        $hmos = HMO::all();

        foreach ($hmos as $hmo) {
            $batchCriteria = $hmo->batch_criteria;
            $orders = Order::where('hmo_id', $hmo->id)
                           ->whereNull('batch_id')
                           ->get();

            foreach ($orders as $order) {
                $batchDate = $batchCriteria === 'submission_date' ? Carbon::parse($order->created_at) : Carbon::parse($order->encounter_date);
                $batchName = $order->provider_name . ' ' . $batchDate->format('M Y');

                $batch = Batch::firstOrCreate([
                    'provider_name' => $order->provider_name,
                    'batch_name' => $batchName,
                    'batch_month' => $batchDate->month,
                    'batch_year' => $batchDate->year
                ]);

                $order->update(['batch_id' => $batch->id]);
            }
        }
    }
}
