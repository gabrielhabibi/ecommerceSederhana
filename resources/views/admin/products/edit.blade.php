@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{ __('product.edit') }} - {{ $product->name }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="id_categories">{{ __('product.category') }}</label>
            <select name="id_categories" class="form-control select2" required>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ $product->id_categories == $category->id ? 'selected' : '' }}>
                        {{ $category->categories }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="name">{{ __('product.product name') }}</label>
            <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
        </div>

        <div class="form-group">
            <label for="description">{{ __('product.description') }}</label>
            <textarea name="description" class="form-control">{{ $product->description }}</textarea>
        </div>

        <div class="form-group">
            <label for="price">{{ __('product.price') }}</label>
            <input type="number" step="0.01" name="price" class="form-control" value="{{ $product->price }}" required>
        </div>

        <div class="form-group">
            <label for="stock">{{ __('product.stock') }}</label>
            <input type="number" name="stock" class="form-control" value="{{ $product->stock }}" required>
        </div>

        <div class="form-group">
            <label>{{ __('product.existing images') }}</label>
            <div class="row">
                @foreach ($product->images as $img)
                    <div class="col-md-2 mb-3">
                        <img src="data:image/jpeg;base64,{{ $img->image }}" class="img-fluid img-thumbnail" />
                    </div>
                @endforeach
            </div>
        </div>

        <div class="form-group">
            <label for="pictures">{{ __('product.add new images') }}</label>
            <input type="file" name="pictures[]" class="form-control" multiple>
            <small class="form-text text-muted">{{ __('product.nb') }}</small>
        </div>

        <button type="submit" class="btn btn-success">{{ __('product.update product') }}</button>
    </form>
@endsection