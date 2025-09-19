@extends('layouts.admin')

@section('content')
<h2 class="mt-3">{{ __('order.list_orders') }}</h2>

<!-- Form Filter + Search -->
<form action="{{ route('orders.index') }}" method="GET" class="mb-3 d-flex flex-wrap gap-2 align-items-end">
    <div>
        <label>{{ __('order.from') }}</label>
        <input type="date" name="from" class="form-control" value="{{ request('from', $from->format('Y-m-d')) }}">
    </div>
    <div>
        <label>{{ __('order.to') }}</label>
        <input type="date" name="to" class="form-control" value="{{ request('to', $to->format('Y-m-d')) }}">
    </div>
    <div>
        <label>{{ __('order.search_order') }}</label>
        <input type="text" name="search" class="form-control" placeholder="{{ __('order.search_order') }}" value="{{ request('search') }}">
    </div>
    <div>
        <button type="submit" class="btn btn-primary mt-1">{{ __('order.filter') }}</button>
    </div>
</form>

<table id="table" class="table table-striped" style="width:100%">
    <thead>
        <tr>
            <th>{{ __('order.order_id') }}</th>
            <th>{{ __('order.user') }}</th>
            <th>{{ __('order.total_price') }}</th>
            <th>{{ __('order.created_at') }}</th>
            <th>{{ __('order.status') }}</th>
            <th>{{ __('order.actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->user->name ?? '-' }}</td>
                <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                <td>{{ $order->created_at->format('d M Y') }}</td>
                <td>
                    <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            @php
                                $allStatuses = ['pending', 'paid', 'failed', 'complete', 'canceled', 'deny', 'expire'];
                                $allowedStatuses = $order->status === 'paid' 
                                    ? ['complete', 'canceled'] 
                                    : $allStatuses;
                            @endphp

                            @foreach($allStatuses as $status)
                                <option value="{{ $status }}" 
                                    {{ $order->status === $status ? 'selected' : '' }}
                                    {{ in_array($status, $allowedStatuses) ? '' : 'disabled' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </td>
                <td>
                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info">
                        {{ __('order.view') }}
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted">{{ __('order.no_orders') }}</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection