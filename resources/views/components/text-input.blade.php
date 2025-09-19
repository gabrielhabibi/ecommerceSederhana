@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' =>
        'bg-white border border-blue-500 text-black rounded-lg
        focus:border-blue-600 focus:ring focus:ring-blue-200 shadow-sm',
]) !!}>
