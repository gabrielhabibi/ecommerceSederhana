@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>{{__('order.order detail')}} : #{{ $order->id }}</h2>
    <p><strong>{{__('order.user')}} :</strong> {{ $order->user->name ?? '-' }}</p>
    <p><strong>{{__('order.total price')}} :</strong> Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
    <p><strong>Status :</strong> {{ ucfirst($order->status) }}</p>
    <p><strong>{{__('order.created at')}} :</strong> {{ $order->created_at->format('d-m-Y') }}</p>

    {{-- Tambahan: Metode Pembayaran --}}
    <p><strong>{{__('order.payment method')}} :</strong> {{ $order->payment_method ?? '-' }}</p>

    <h4>{{__('order.Order Items')}}</h4>

    <table id="table" class="table table-striped" style="width:100%">
        <thead>
            <tr>
                <th>{{ __('order.Product') }}</th>
                <th>{{ __('order.Quantity') }}</th>
                <th>{{ __('order.Price') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product->name ?? '-' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('orders.index') }}" class="btn btn-secondary">Back to List</a>
</div>
@endsection