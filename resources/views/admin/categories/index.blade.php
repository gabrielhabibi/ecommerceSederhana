@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{ __('categories.list') }}</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Header atas --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('categories.create') }}" class="btn btn-primary">
            {{ __('categories.add_button') }}
        </a>
        <form action="{{ route('categories.index') }}" method="GET" class="d-flex" style="max-width: 300px;">
            <input type="text" name="search" class="form-control me-2" 
                   placeholder="{{ __('categories.search') }}..." 
                   value="{{ request('search') }}">
            <button type="submit" class="btn btn-secondary">{{ __('categories.search') }}</button>
        </form>
    </div>

    {{-- Card utama --}}
    <div class="card shadow-sm">
        <div class="card-body">
            {{-- Tombol atas --}}
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <a href="{{ route('categories.template') }}" class="btn btn-warning me-2">
                        {{ __('categories.download_template') }}
                    </a>
                    <a href="{{ route('categories.export') }}" class="btn btn-success">
                        {{ __('categories.export') }}
                    </a>
                </div>
                <form action="{{ route('categories.import') }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center">
                    @csrf
                    <input type="file" name="file" class="form-control me-2 bg-white" style="max-width: 250px;" required>
                    <button type="submit" class="btn btn-info">{{ __('categories.import') }}</button>
                </form>
            </div>

            {{-- Tabel --}}
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th class="text-center" style="white-space: nowrap;">{{ __('categories.no') }}</th>
                            <th class="text-center" style="white-space: nowrap;">{{ __('categories.category_name') }}</th>
                            <th class="text-center" style="white-space: nowrap;">{{ __('categories.description') }}</th>
                            <th class="text-center" style="white-space: nowrap;">{{ __('categories.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $key => $category)
                            <tr>
                                <td>{{ $categories->firstItem() + $key }}</td>
                                <td>{{ $category->categories }}</td>
                                <td>{{ $category->description }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('categories.edit', $category->id) }}" 
                                           class="btn btn-sm btn-warning">
                                            {{ __('categories.edit_button') }}
                                        </a>
                                        <form action="{{ route('categories.destroy', $category->id) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('{{ __('categories.confirm_delete') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                {{ __('categories.delete_button') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">{{ __('categories.empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3 d-flex justify-content-center">
                {{ $categories->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection