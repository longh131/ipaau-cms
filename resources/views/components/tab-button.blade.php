@props([
    'label',
    'active' => false,
])

<button
    type="button"
    @class([
        'text-secondary tab label-md lg:label-xl transition-colors duration-200 grid grid-cols-1 items-center gap-2 w-auto max-w-full',
        'active' => $active,
        'hover:text-primary' => ! $active,
    ])
>
    <span class="block text-left col-start-1 whitespace-normal">{{ $label }}</span>
</button>
