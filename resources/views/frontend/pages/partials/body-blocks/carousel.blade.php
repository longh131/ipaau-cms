@php
    /** @var array{heading: string, slides: array<int, array{quote: string, author: string}>} $block */
    $slides = $block['slides'] ?? [];
@endphp

@include('partials.testimonials.text-only-carousel-section', [
    'heading' => $block['heading'] ?? '',
    'slides' => $slides,
    'sectionClass' => 'cms-body-block cms-body-block--carousel',
])
