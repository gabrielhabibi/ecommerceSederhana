@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">{{ __('role.management') }}</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-striped align-middle">
        <thead class="bg-primary text-white rounded">
            <tr>
                <th style="width: 20%">{{ __('role.role') }}</th>
                <th style="width: 60%">{{ __('role.permissions') }}</th>
                <th style="width: 20%">{{ __('role.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
                <tr>
                    <form action="{{ route('roles.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <td><strong>{{ ucfirst($role->role_name) }}</strong></td>
                        <td>
                            <div class="row">
                                @foreach($allPermissions as $perm)
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input 
                                                class="form-check-input" 
                                                type="checkbox" 
                                                name="permissions[]" 
                                                value="{{ $perm }}"
                                                {{ in_array($perm, $role->permissions ?? []) ? 'checked' : '' }}
                                                {{ $role->role_name === 'super admin' ? 'disabled' : '' }}
                                            >
                                            <label class="form-check-label">
                                                {{ __('role.' . strtolower($perm)) }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="text-center">
                            @if($role->role_name !== 'super admin')
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-save"></i> {{ __('role.save') }}
                                </button>
                            @else
                                <span class="badge bg-success">{{ __('role.all_access') }}</span>
                            @endif
                        </td>
                    </form>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection