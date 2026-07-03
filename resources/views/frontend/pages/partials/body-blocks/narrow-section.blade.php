@php
    /** @var array{type: string} $block */
    $blockType = $block['type'] ?? 'rich_text';
@endphp

<section
    data-type="copyBlock"
    class="cms-body-block cms-body-block--narrow py-8 lg:py-12 about-section bg-[color:var(--bg-color)]"
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
            <div class="max-w-4xl mx-auto w-full">
                @include('frontend.pages.partials.body-blocks.'.$blockType, [
                    'block' => $block,
                ])
            </div>
        </div>
    </div>
</section>
