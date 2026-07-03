@php
    $stats = ['items' => $block['items'] ?? []];
@endphp

@if(! empty($stats['items']))
    <div class="cms-body-block cms-body-block--stats">
        @include('sections.home.stats', ['stats' => $stats])
    </div>
@endif
