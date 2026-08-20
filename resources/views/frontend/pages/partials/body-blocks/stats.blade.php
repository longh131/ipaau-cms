@php
    $stats = ['items' => $block['items'] ?? []];
    $layout = $layout ?? 'default';
    $itemCount = count($stats['items']);
    $useThreeColumnStyle = $itemCount === 3;
@endphp

@if(! empty($stats['items']))
    <div @class([
        'cms-body-block cms-body-block--stats',
        'cms-body-block--stats-three' => $useThreeColumnStyle,
        'cms-body-block--stats-four' => ! $useThreeColumnStyle,
        'cms-governance-module cms-general-secondary-module' => $layout === 'general_secondary',
    ])>
        @include('sections.home.stats', ['stats' => $stats])
    </div>
@endif
