@php
    $layout = $layout ?? 'default';
    $testimonials = [
        'section_title' => trim((string) ($block['section_title'] ?? '')),
        'items' => $block['items'] ?? [],
    ];
    $hasItems = ($testimonials['items'] ?? []) !== [];
    $hasSectionTitle = filled($testimonials['section_title']);
@endphp

@if($hasItems || $hasSectionTitle)
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
