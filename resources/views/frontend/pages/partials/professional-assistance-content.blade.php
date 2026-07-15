@php
    use App\Support\PageTemplate\ProfessionalAssistanceSections;

    $sections = $pageView['sections'] ?? [];
    $hasBreadcrumbs = $hasBreadcrumbs ?? false;
@endphp

@if($pageView['has_content'] ?? false)
    <section
        data-type="cmsPageContent"
        @class([
            'cms-page-content-section cms-professional-assistance-page',
            'cms-page-content-section--with-breadcrumb' => $hasBreadcrumbs,
            'pt-28' => ! $hasBreadcrumbs,
        ])
    >
        @foreach ($sections as $section)
            @php
                $sectionType = (string) ($section['type'] ?? '');
            @endphp

            @if($sectionType === ProfessionalAssistanceSections::TYPE_RICH_TEXT)
                @include('frontend.pages.partials.body-blocks.narrow-section', [
                    'block' => $section,
                    'layout' => 'professional_assistance',
                ])
            @elseif($sectionType === ProfessionalAssistanceSections::TYPE_NEWS_LIST_A)
                @include('frontend.pages.partials.body-blocks.news_list', [
                    'block' => $section,
                    'layout' => 'default',
                ])
            @elseif(in_array($sectionType, [
                ProfessionalAssistanceSections::TYPE_HTML_BODY,
                ProfessionalAssistanceSections::TYPE_MEDIA_SPLIT,
                ProfessionalAssistanceSections::TYPE_CAROUSEL,
            ], true))
                @include('frontend.pages.partials.body-blocks.'.$sectionType, [
                    'block' => $section,
                    'layout' => $sectionType === ProfessionalAssistanceSections::TYPE_HTML_BODY
                        ? 'professional_assistance'
                        : 'default',
                ])
            @endif
        @endforeach

        @include('frontend.pages.partials.page-content-footer-spacer')
    </section>
@endif
