@extends('layouts.admin')

@section('content')
    <h2 class="mt-3"><i class="fas fa-bell"></i> {{ __('notification.notifications') }}</h2>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($notifications->count())
        <div class="card mt-3 shadow-sm">
            <div class="card-header bg-primary text-white">
                <strong><i class="fas fa-inbox"></i> {{ __('notification.list') }}</strong>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('notification.no') }}</th>
                            <th>{{ __('notification.message') }}</th>
                            <th>{{ __('notification.status') }}</th>
                            <th>{{ __('notification.received') }}</th>
                            <th>{{ __('notification.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($notifications as $index => $notification)
                            @php
                                $data = is_array($notification->data)
                                    ? $notification->data
                                    : json_decode($notification->data, true);
                                $createdAt = \Carbon\Carbon::parse($notification->created_at)->diffForHumans();
                            @endphp

                            <tr class="{{ is_null($notification->read_at) ? 'table-info' : '' }}">
                                <td>{{ $index + $notifications->firstItem() }}</td>
                                <td>
                                    @if ($notification->type === 'App\Notifications\NewUserRegistered')
                                        <i class="fas fa-user-plus text-success"></i>
                                        {!! __('notification.new_user', ['name' => $data['name'] ?? 'Unknown User']) !!}
                                    @elseif ($notification->type === 'App\Notifications\NewOrder')
                                        <i class="fas fa-shopping-cart text-warning"></i>
                                        Pesanan baru dengan No. Pesanan 
                                        <strong>#{{ $data['order_id'] ?? '-' }}</strong> 
                                        dari <strong>{{ $data['user_name'] ?? 'Unknown' }}</strong> 
                                        statusnya 
                                        <span class="badge 
                                            @if(($data['status'] ?? '') === 'success') bg-success 
                                            @elseif(($data['status'] ?? '') === 'pending') bg-warning 
                                            @elseif(($data['status'] ?? '') === 'failed') bg-danger 
                                            @else bg-secondary @endif">
                                            {{ ucfirst($data['status'] ?? 'unknown') }}
                                        </span>
                                    @elseif ($notification->type === 'App\Notifications\OrderStatusChanged')
                                        {!! __('notification.order_status', [
                                            'order_id' => $data['order_id'] ?? '-',
                                            'status' => ucfirst($data['new_status'] ?? '-'),
                                        ]) !!}
                                    @elseif ($notification->type === 'App\Notifications\NewProduct')
                                        <i class="fas fa-box text-primary"></i>
                                        {!! __('notification.new_product', ['product' => $data['message'] ?? '-' ]) !!}
                                    @endif
                                </td>
                                <td>
                                    @if (is_null($notification->read_at))
                                        <span class="badge bg-success">{{ __('notification.new') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('notification.read') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <i class="far fa-clock"></i> {{ $createdAt }}
                                </td>
                                <td class="d-flex gap-2">
                                    @if (is_null($notification->read_at))
                                        <a href="{{ route('notifications.show', $notification->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> {{ __('notification.view') }}
                                        </a>
                                    @endif
                                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">
                                            <i class="fas fa-trash"></i> {{ __('notification.delete') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $notifications->links('pagination::bootstrap-5') }}
        </div>
    @else
        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle"></i> {{ __('notification.no_notifications') }}
        </div>
    @endif
@endsection