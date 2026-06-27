@php
    /** @var array<string, bool> $sectionActive */
    $sectionActive = $sectionActive ?? [];
@endphp
@if($sectionActive['hero'] ?? false)
@include('sections.home.hero', ['hero' => $hero ?? null])
@endif
@if($sectionActive['footnote-cards'] ?? false)
@include('sections.home.footnote-cards', ['footnoteCards' => $footnoteCards ?? ['items' => []]])
@endif
@if($sectionActive['membership'] ?? false)
@include('sections.home.membership', ['membership' => $membership ?? null])
@endif
@if($sectionActive['stats'] ?? false)
@include('sections.home.stats', ['stats' => $stats ?? ['items' => []]])
@endif
@include('partials.decorators.decorator-1')
@if($sectionActive['cpd-intro'] ?? false)
@include('sections.home.cpd-intro', ['cpdIntro' => $cpdIntro ?? null])
@endif
@if($sectionActive['tabbed-content'] ?? false)
@include('sections.home.tabbed-content', ['tabbedContent' => $tabbedContent ?? ['tabs' => []]])
@endif
@if($sectionActive['testimonials'] ?? false)
@include('sections.home.testimonials', ['testimonials' => $testimonials ?? ['items' => []]])
@endif
@include('partials.decorators.decorator-2')
@if($sectionActive['about-intro'] ?? false)
@include('sections.home.about-intro', ['aboutIntro' => $aboutIntro ?? null])
@endif
@if($sectionActive['diversity'] ?? false)
@include('sections.home.diversity', ['diversity' => $diversity ?? null])
@endif
@if($sectionActive['cta-section'] ?? false)
@include('sections.home.cta-section', ['ctaSection' => $ctaSection ?? null])
@endif
@include('partials.decorators.decorator-3')
@if($sectionActive['faq'] ?? false)
@include('sections.home.faq', ['faq' => $faq ?? ['items' => []]])
@endif
@if($sectionActive['newsletter'] ?? false)
@include('sections.home.newsletter', ['newsletter' => $newsletter ?? null])
@endif
