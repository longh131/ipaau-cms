@php
    $layout = $layout ?? 'default';
    $testimonials = [
        'section_title' => trim((string) ($block['section_title'] ?? '')),
        'items' => $block['items'] ?? [],
    ];
    $items = $testimonials['items'];
    $hasItems = $items !== [];
    $hasSectionTitle = filled($testimonials['section_title']);
    $useTextOnlyCarousel = $layout === 'general_secondary'
        && collect($items)->every(fn (array $item): bool => blank($item['image'] ?? null));
@endphp

@if($hasItems || $hasSectionTitle)
    @if($useTextOnlyCarousel)
        @php
            $slides = collect($items)
                ->map(fn (array $item): array => [
                    'quote' => trim((string) ($item['content'] ?? '')),
                    'author' => trim((string) ($item['title'] ?? '')),
                ])
                ->filter(fn (array $slide): bool => filled($slide['quote']) || filled($slide['author']))
                ->values()
                ->all();
        @endphp

        @include('partials.testimonials.text-only-carousel-section', [
            'heading' => $testimonials['section_title'],
            'slides' => $slides,
            'sectionClass' => 'cms-body-block cms-body-block--carousel cms-governance-module cms-general-secondary-module',
        ])
    @else
        <div @class([
            'cms-body-block cms-body-block--testimonials',
            'cms-governance-module cms-general-secondary-module' => $layout === 'general_secondary',
        ])>
            @include('sections.home.testimonials', [
                'testimonials' => $testimonials,
                'plainBackground' => $layout === 'general_secondary',
            ])
        </div>
    @endif
@endif
