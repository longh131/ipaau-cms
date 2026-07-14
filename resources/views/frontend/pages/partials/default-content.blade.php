@php
    $bodyBlocks = $pageView['body_blocks'] ?? [];
    $fullWidthBlockTypes = ['tabs', 'carousel', 'media_split', 'content_columns', 'faq', 'stats', 'card_list_curated', 'news_list', 'html_body'];
    $hasBreadcrumbs = $hasBreadcrumbs ?? false;
@endphp

@if(! empty($bodyBlocks))
    <section
        data-type="cmsPageContent"
        @class([
            'cms-page-content-section',
            'cms-page-content-section--with-breadcrumb' => $hasBreadcrumbs,
            'pt-28' => ! $hasBreadcrumbs,
        ])
    >
        @foreach ($bodyBlocks as $block)
            @if(in_array($block['type'], $fullWidthBlockTypes, true))
                @include('frontend.pages.partials.body-blocks.'.$block['type'], [
                    'block' => $block,
                    'layout' => 'default',
                ])
            @else
                @include('frontend.pages.partials.body-blocks.narrow-section', [
                    'block' => $block,
                ])
            @endif
        @endforeach

        @include('frontend.pages.partials.page-content-footer-spacer')
    </section>
@endif
