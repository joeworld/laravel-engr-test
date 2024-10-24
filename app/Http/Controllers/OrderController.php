<?php

namespace App\Http\Controllers;

use App\Actions\SubmitOrder;
use App\Http\Requests\SubmitOrderRequest;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function create()
    {
        return Inertia::render('SubmitOrder');
    }

    public function store(SubmitOrderRequest $request)
    {
        // Use the SubmitOrder action to handle the order submission
        $order = SubmitOrder::run($request);

        if ($order) {
            return redirect()->route('orders.create')->with('success_message', 'Order submitted successfully.');
        }
        
        return redirect()->route('orders.create')->with('error_message', 'It seems an error occurred.');
    }
}