<?php

namespace App\Actions;

use App\Http\Requests\SubmitOrderRequest;
use App\Jobs\SendOrderEmail;
use App\Models\Order;
use App\Models\Batch;
use App\Models\OrderItem;
use App\Models\Hmo;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class SubmitOrder
{
    use AsAction;

    /**
     * Handle the order submission logic.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(SubmitOrderRequest $request): Order
    {
        // Find the HMO based on the code provided
        $hmo = Hmo::where('code', $request->code)->firstOrFail();

        // Determine the batch date based on the HMO's criteria
        $batchDate = $hmo->batch_criteria === 'submission_date' ? now() : Carbon::parse($request->encounter_date);
        $batchName = $request->provider_name . ' ' . $batchDate->format('M Y');

        // Find or create the batch for the given provider and date
        $batch = Batch::firstOrCreate([
            'provider_name' => $request->provider_name,
            'batch_name' => $batchName,
            'batch_month' => $batchDate->month,
            'batch_year' => $batchDate->year
        ]);

        // Create the order and calculate the total amount
        $orderAmount = 0;
        $order = Order::create([
            'provider_name' => $request->provider_name,
            'hmo_id' => $hmo->id,
            'encounter_date' => $request->encounter_date,
            'batch_id' => $batch->id,
            'order_amount' => $orderAmount
        ]);

        // Process each item in the order
        foreach ($request->items as $item) {
            $subtotal = $item['unit_price'] * $item['quantity'];
            $orderAmount += $subtotal;

            // Create the order item
            OrderItem::create([
                'order_id' => $order->id,
                'item_name' => $item['name'],
                'unit_price' => $item['unit_price'],
                'quantity' => $item['quantity'],
                'subtotal' => $subtotal
            ]);
        }

        // Update the order's total amount
        $order->update(['order_amount' => $orderAmount]);

        // Send email
        SendOrderEmail::dispatch($order);

        return $order;
    }
}