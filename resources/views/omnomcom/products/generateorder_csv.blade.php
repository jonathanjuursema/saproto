<pre>
@foreach ($orders as $order)
{{ $order->product->supplier_id ?? 'NO_SUPPLIER_ID' }};{{ $order->order_collo }};OmNomCom Auto Order - {{ $order->product->name }}
@endforeach
</pre>
