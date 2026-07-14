@php
    $newsletter = [
        'title' => $block['title'] ?? '',
        'content_html' => $block['content_html'] ?? '',
        'button_text' => $block['button_text'] ?? '提交',
    ];
    $layout = $layout ?? 'default';
@endphp

@if(filled($newsletter['title']) || filled(strip_tags((string) $newsletter['content_html'])))
    <div @class([
        'cms-body-block cms-body-block--newsletter',
        'cms-governance-module cms-general-secondary-module' => $layout === 'general_secondary',
    ])>
        @include('sections.home.newsletter', ['newsletter' => $newsletter])
    </div>
@endif
