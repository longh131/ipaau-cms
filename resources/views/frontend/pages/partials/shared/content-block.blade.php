@php
    /** @var array{
     *     title: string,
     *     title_align: string,
     *     content_html: string,
     *     button: ?array{label: string, url: string, style: string, target: string},
     *     buttons: array<int, array{label: string, url: string, style: string, target: string}>
     * } $block */
    $block = $block ?? [];
    $title = trim((string) ($block['title'] ?? ''));
    $titleAlign = (string) ($block['title_align'] ?? 'left');
    $contentHtml = (string) ($block['content_html'] ?? '');
    $buttons = $block['buttons'] ?? [];

    if ($buttons === [] && filled($block['button'] ?? null)) {
        $buttons = [$block['button']];
    }

    $hasTitle = filled($title);
    $hasContent = filled(strip_tags($contentHtml));
    $textAlignClass = match ($titleAlign) {
        'center' => 'text-center',
        'right' => 'text-right',
        default => 'text-left',
    };
    $buttonAlignClass = $titleAlign === 'center' ? 'justify-center' : ($titleAlign === 'right' ? 'justify-end' : 'justify-start');
    $sectionClass = trim((string) ($sectionClass ?? 'py-12 cms-governance-module cms-governance-content-block'));
@endphp

@if($hasTitle || $hasContent || $buttons !== [])
    <section
        data-type="basicContentWithColumns"
        @class([$sectionClass])
    >
        <div class="inner container px-4 md:px-10 mx-auto">
            <div class="column-wrapper grid items-stretch grid-cols-1 {{ $textAlignClass }}">
                <div class="column flex flex-col h-full pb-5">
                    <div class="w-full {{ $textAlignClass }}">
                        @if($hasTitle)
                            <h3 class="cms-section-title mb-0 {{ $textAlignClass }}">
                                <span class="text-secondary">{{ $title }}</span>
                            </h3>
                        @endif

                        @if($hasContent)
                            <div
                                @class([
                                    'cms-page-content cms-governance-content-block__body text-[color:var(--ipa-color)] text-xl font-din text-primary cms-rich-text__body',
                                    'mt-4' => $hasTitle,
                                    $textAlignClass,
                                ])
                                data-type="section-description"
                                data-rte="true"
                            >
                                {!! $contentHtml !!}
                            </div>
                        @endif
                    </div>

                    @if($buttons !== [])
                        <div class="basis-auto flex flex-col sm:flex-row {{ $buttonAlignClass }} flex-wrap gap-6 mt-12 mb-6">
                            @foreach ($buttons as $button)
                                <x-cta-button
                                    :label="$button['label']"
                                    :url="$button['url']"
                                    :style="$button['style']"
                                    :target="filled($button['target'] ?? null) ? $button['target'] : null"
                                />
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endif
