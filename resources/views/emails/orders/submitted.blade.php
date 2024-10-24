@component('mail::message')
# New Order Submitted

A new order has been submitted by **{{ $order->provider_name }}**.

**Order Details:**
- Order ID: {{ $order->id }}
- Provider: {{ $order->provider_name }}
- Encounter Date: {{ $order->encounter_date->format('Y-m-d') }}
- Total Amount: NGN{{ number_format($order->order_amount, 2) }}

@component('mail::button', ['url' => url('/orders/' . $order->id)])
View Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent