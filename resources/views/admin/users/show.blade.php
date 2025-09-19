@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>{{ __('user.user detail') }} : {{ $user->name }}</h2>
    <p><strong>{{ __('user.email') }} :</strong> {{ $user->email }}</p>
    <p><strong>{{ __('user.role') }} :</strong> {{ $user->role->role_name ?? '-' }}</p>
    
    <h4>{{ __('user.order history') }}</h4>

    {{-- Card --}}
    <div class="card shadow-sm">
        <div class="card-body">
            {{-- Toolbar atas --}}
            <div class="mb-3">
                <a href="{{ route('users.exportOrders', $user->id) }}" class="btn btn-success">
                    {{ __('user.export orders') }}
                </a>
            </div>

            {{-- Table (scrollable) --}}
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table id="table" class="table table-striped align-middle text-center" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('user.order id') }}</th>
                            <th>{{ __('user.total price') }}</th>
                            <th>{{ __('user.status') }}</th>
                            <th>{{ __('user.created at') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($user->orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td class="text-start">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            <td>{{ ucfirst($order->status) }}</td>
                            <td>{{ $order->created_at->format('Y-m-d') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4">{{ __('user.no orders found') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tombol kembali di kanan bawah --}}
            <div class="d-flex justify-content-end mt-3">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    {{ __('user.back to list') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection