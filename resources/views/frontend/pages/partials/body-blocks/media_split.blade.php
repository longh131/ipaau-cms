@php
    /** @var array{
     *     image_position: string,
     *     image_shape: string,
     *     image: ?string,
     *     tagline: string,
     *     title: string,
     *     content_html: string,
     *     buttons: array<int, array{label: string, url: string, style: string, target: string}>
     * } $block */
    $imageLeft = ($block['image_position'] ?? 'left') === 'left';
    $hasImage = filled($block['image'] ?? null);
    $imageShape = ($block['image_shape'] ?? 'acorn') === 'rectangle' ? 'rectangle' : 'acorn';
    $hasText = filled($block['tagline'] ?? null)
        || filled($block['title'] ?? null)
        || filled(strip_tags((string) ($block['content_html'] ?? '')))
        || ! empty($block['buttons'] ?? []);
    $layout = $layout ?? 'default';
    $isGeneralSecondary = $layout === 'general_secondary';
@endphp

@if($hasImage || $hasText)
    <section
        data-type="ctaSection"
        @class([
            'cms-body-block cms-body-block--media-split overflow-hidden about-section bg-[color:var(--bg-color)]',
            'cms-governance-module cms-general-secondary-module' => $isGeneralSecondary,
        ])
        style="
            --bg-color: transparent;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
        "
    >
        <div class="inner container px-4 md:px-10 mx-auto flex flex-col gap-12">
            <div class="container mx-auto px-4">
                <div
                    @class([
                        'grid grid-cols-1 items-center gap-14 lg:gap-20 about-cta',
                        'lg:grid-cols-[40%_1fr]' => $imageLeft,
                        'lg:grid-cols-[1fr_40%] about-cta--image-right' => ! $imageLeft,
                    ])
                >
                    {{-- 与原版 CtaSection 一致：图片始终在 DOM 中靠前，靠 grid 列定位控制左右 --}}
                    @include('frontend.pages.partials.body-blocks.partials.media-split-image', [
                        'block' => $block,
                        'hasImage' => $hasImage,
                        'imageShape' => $imageShape,
                        'imageLeft' => $imageLeft,
                    ])
                    @include('frontend.pages.partials.body-blocks.partials.media-split-text', [
                        'block' => $block,
                        'hasText' => $hasText,
                        'hasImage' => $hasImage,
                        'imageLeft' => $imageLeft,
                    ])
                </div>
            </div>
        </div>
    </section>
@endif
