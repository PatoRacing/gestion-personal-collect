@php
    $classes = "text-blue-800 text-sm font-bold"
@endphp
<a {{$attributes->merge(['class'=> $classes])}} >
    {{ $slot }}
</a>