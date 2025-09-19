@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{ __('categories.add') }}</h2>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
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

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="categories" class="form-label">{{ __('categories.category_name') }}</label>
                    <input type="text" name="categories" id="categories" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">{{ __('categories.description') }}</label>
                    <textarea name="description" id="description" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('categories.create_category') }}</button>
            </form>
        </div>
    </div>
@endsection
