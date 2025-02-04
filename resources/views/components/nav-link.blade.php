@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-3 rounded bg-blue-800 hover:bg-blue-900 text-white text-xs font-bold leading-5 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 text-xs font-bold leading-5 text-black hover:text-gray-900 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>