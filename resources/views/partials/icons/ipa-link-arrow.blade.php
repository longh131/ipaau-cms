@php
    $variant = $variant ?? 'light';
    $stemColor = $variant === 'dark' ? '#ffffff' : '#992785';
    $headColor = $variant === 'dark' ? '#ffffff' : '#0d2c6c';
@endphp

<svg
    fill="none"
    height="24"
    viewBox="0 0 25 24"
    width="25"
    xmlns="http://www.w3.org/2000/svg"
    aria-hidden="true"
    class="shrink-0 {{ $class ?? '' }}"
>
    <g stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7487">
        <path d="m20.083 11.7256h-14.99999" stroke="{{ $stemColor }}" />
        <path d="m14.0337 5.701 6.05 6.024-6.05 6.025" stroke="{{ $headColor }}" />
    </g>
</svg>
