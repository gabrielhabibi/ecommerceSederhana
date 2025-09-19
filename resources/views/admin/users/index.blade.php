@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{ __('user.list users') }}</h2>

    {{-- Search bar --}}
    <div class="d-flex justify-content-end mb-3">
        <form action="{{ route('users.index') }}" method="GET" class="d-flex" style="max-width: 300px;">
            <input type="text" name="search" class="form-control me-2"
                   placeholder="{{ __('user.search') }}..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-secondary">{{ __('user.search') }}</button>
        </form>
    </div>

    {{-- Card --}}
    <div class="card shadow-sm">
        <div class="card-body">
            {{-- Toolbar --}}
            <div class="mb-3">
                <a href="{{ route('export.user') }}" class="btn btn-success">
                    {{ __('user.export') }}
                </a>
            </div>

            {{-- Table (scrollable) --}}
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-striped align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>{{ __('user.name') }}</th>
                            <th>{{ __('user.email') }}</th>
                            <th>{{ __('user.role') }}</th>
                            <th>{{ __('user.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                            <tr>
                                {{-- Nomor urut sesuai pagination --}}
                                <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                <td class="text-start">{{ $user->name }}</td>
                                <td class="text-start">{{ $user->email }}</td>
                                <td>{{ $user->role->role_name ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-info btn-sm">
                                        {{ __('user.view') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">{{ __('user.no users found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
