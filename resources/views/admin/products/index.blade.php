@extends('layouts.admin')

@section('content')
<h2 class="mt-3">{{ __('product.products') }}</h2>

{{-- Notifikasi --}}
@if (session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- Tombol tambah + search --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('products.create') }}" class="btn btn-primary">
        {{ __('product.add') }}
    </a>
    <form action="{{ route('products.index') }}" method="GET" class="d-flex" style="max-width: 300px;">
        <input type="text" name="search" class="form-control me-2"
            placeholder="Cari produk..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-secondary">Cari</button>
    </form>
</div>

{{-- Card utama --}}
<div class="card shadow-sm">
    <div class="card-body">
        {{-- Toolbar dalam kotak --}}
        <div class="d-flex justify-content-between mb-3">
            <div>
                <a href="{{ route('products.template') }}" class="btn btn-warning me-2">Download Template</a>
                <a href="{{ route('products.export') }}" class="btn btn-success">Export Excel</a>
            </div>
            <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center">
                @csrf
                <input type="file" name="file" class="form-control me-2 bg-white" style="max-width: 220px;" required>
                <button type="submit" class="btn btn-info">Import Excel</button>
            </form>
        </div>

        {{-- Tabel dengan scroll --}}
        <div style="max-height: 500px; overflow-y: auto; overflow-x: auto;" class="table-responsive">
            <table class="table table-striped text-center align-middle" style="min-width: 1200px;">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>{{ __('product.name') }}</th>
                        <th>{{ __('product.description') }}</th>
                        <th>{{ __('product.price') }}</th>
                        <th>{{ __('product.stock') }}</th>
                        <th>{{ __('product.category') }}</th>
                        <th>{{ __('product.images') }}</th>
                        <th>{{ __('product.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $index => $product)
                        <tr>
                            {{-- Nomor urut --}}
                            <td>{{ $products->firstItem() + $index }}</td>
                            <td class="text-start">{{ $product->name }}</td>
                            <td class="text-start">{{ $product->description }}</td>
                            <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            <td>{{ $product->stock }}</td>
                            <td>{{ $product->category->categories ?? '-' }}</td>
                            <td>
                                @foreach($product->images as $img)
                                    <img src="data:image/jpeg;base64,{{ $img->image }}" alt="gambar" width="50" class="mb-1">
                                @endforeach
                            </td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-warning me-2">Edit</a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">Belum ada produk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-3">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection