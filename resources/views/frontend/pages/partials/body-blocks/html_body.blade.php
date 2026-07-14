@php
    $bodyHtml = (string) ($block['body_html'] ?? '');
    $hasBody = filled(strip_tags($bodyHtml));
    $layout = $layout ?? 'default';
@endphp

@if($hasBody)
    <section
        @class([
            'cms-body-block cms-body-block--html-body',
            'cms-governance-module cms-general-secondary-module' => $layout === 'general_secondary',
        ])
    >
        <div class="inner container px-4 md:px-10 mx-auto">
            <div class="about-rich-text cms-page-content cms-basic-content__body cms-basic-content__body--html font-din text-[color:var(--ipa-color)]">
                {!! $bodyHtml !!}
            </div>
        </div>
    </section>
@endif
