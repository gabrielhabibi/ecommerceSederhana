@extends('layouts.admin')

@section('content')
    <!-- Flash Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
<h2 class="mt-3">{{ __('admin.list_admins') }}</h2>

<!-- Bar atas: Add Admin + Search -->
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
    <a href="{{ route('admins.create') }}" class="btn btn-primary mb-2">
        {{ __('admin.add_admin') }}
    </a>

    <form action="{{ route('admins.index') }}" method="GET" class="d-flex mb-2">
        <input type="text" name="search" class="form-control me-2" placeholder="{{ __('admin.search_admin') }}" value="{{ request('search') }}">
        <button type="submit" class="btn btn-outline-primary">{{ __('admin.search') }}</button>
    </form>
</div>

<!-- Card scrollable -->
<div class="card p-3">
    <!-- Tombol Export di dalam card -->
    <div class="mb-3 text-end">
        <a href="{{ route('admins.export') }}" class="btn btn-success">
            {{ __('admin.export_admins') }}
        </a>
    </div>

    <!-- Tabel scrollable -->
    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
        <table class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>{{ __('admin.no') }}</th>
                    <th>{{ __('admin.name') }}</th>
                    <th>{{ __('admin.email') }}</th>
                    <th>{{ __('admin.role') }}</th>
                    <th>{{ __('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($admins as $admin)
                    <tr>
                        <td>{{ $loop->iteration }}</td> <!-- Nomor urut -->
                        <td>{{ $admin->name }}</td>
                        <td>{{ $admin->email }}</td>
                        <td>{{ $admin->role->role_name ?? '-' }}</td>
                        <td>
                            <a href="{{ route('admins.edit', $admin->id) }}" class="btn btn-sm btn-primary">
                                {{ __('admin.edit_admin') }}
                            </a>
                            <form action="{{ route('admins.destroy', $admin->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('{{ __('admin.success_delete') }}')">
                                    {{ __('admin.delete') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                <tr>
                        <td colspan="5" class="text-center">{{ __('admin.no_admins') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection