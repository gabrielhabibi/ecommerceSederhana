@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{__('dashboard.admin dashboard')}}</h2>
    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">{{__('dashboard.total users')}}</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $totalUsers }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">{{__('dashboard.total products')}}</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $totalProducts }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-danger mb-3">
                <div class="card-header">{{__('dashboard.total orders')}}</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $totalOrders }}</h5>
                </div>
            </div>
        </div>
    </div>

    <h4>{{__('dashboard.recent orders')}}</h4>
        <div class="table-responsive" style="max-height: 300px; overflow-y:auto;">
            <table id="table" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>No. </th>
                        <th>{{__('dashboard.date')}}</th>
                        <th>{{__('dashboard.total orders')}}</th>
                        <th>{{__('dashboard.total price')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $index => $order)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}</td>
                            <td>{{ $order->total_orders }}</td>
                            <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">{{ __('dashboard.no recent orders') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
@endsection