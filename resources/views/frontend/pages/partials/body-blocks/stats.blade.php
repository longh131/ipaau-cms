@php
    $stats = ['items' => $block['items'] ?? []];
    $layout = $layout ?? 'default';
@endphp

@if(! empty($stats['items']))
    <div @class([
        'cms-body-block cms-body-block--stats',
        'cms-governance-module cms-general-secondary-module' => $layout === 'general_secondary',
    ])>
        @include('sections.home.stats', ['stats' => $stats])
    </div>
@endif
