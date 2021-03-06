<?php

use App\Models\Order;

?>
@extends( 'layout.dashboard' )

<?php
$isDue  =   in_array( $order->payment_status, [ Order::PAYMENT_PARTIALLY, Order::PAYMENT_UNPAID ] );
?>

@section( 'layout.dashboard.body' )
<div>
    @include( '../common/dashboard-header' )
    <div id="dashboard-content" class="px-4">
        <div class="page-inner-header mb-4">
            <h3 class="text-3xl text-gray-800 font-bold">{!! sprintf( __( 'Invoice &mdash; %s' ), $order->code ) !!}</h3>
            <p class="text-gray-600">{{ __( 'Order invoice' ) }}</p>
        </div>
        <div class="my-2">
            <ns-order-invoice 
                :shipping='@json( $shipping )'
                :billing='@json( $billing )'
                :order='@json( $order )'></ns-order-invoice>
        </div>
    </div>
</div>
@endsection