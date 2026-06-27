@props([
    'label',
    'active' => false,
])

<button
    type="button"
    @class([
        'text-secondary tab label-md lg:label-xl transition-colors duration-200 grid grid-cols-1 items-center gap-2',
        'active' => $active,
        'hover:text-primary' => ! $active,
    ])
>
    <span
        class="max-w-none md:max-w-56 truncate text-nowrap ellipsis whitespace-nowrap block text-left col-start-1"
    >{{ $label }}</span>
</button>
