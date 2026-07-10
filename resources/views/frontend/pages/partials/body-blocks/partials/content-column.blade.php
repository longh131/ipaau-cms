@php
    /** @var array{
     *     title: string,
     *     content_html: string,
     *     button: ?array{label: string, url: string, style: string, target: string}
     * } $column */
    $hasTitle = filled($column['title'] ?? null);
    $hasContent = filled(strip_tags((string) ($column['content_html'] ?? '')));
    $button = $column['button'] ?? null;
@endphp

<div class="max-lg:pt-5 first:max-lg:pt-0 column flex flex-col h-full pb-5 border-solid">
    <div class="text-left w-full">
        @if($hasTitle)
            <h3 class="cms-section-title mb-0 text-left">
                <span class="text-secondary">{{ $column['title'] }}</span>
            </h3>
        @endif

        @if($hasContent)
            <div
                @class([
                    'cms-page-content text-[color:var(--ipa-color)] text-xl font-din text-primary',
                    'mt-4' => $hasTitle,
                ])
                data-type="section-description"
                data-rte="true"
            >
                {!! $column['content_html'] !!}
            </div>
        @endif
    </div>

    @if(filled($button))
        <div class="basis-auto flex flex-col sm:flex-row justify-start flex-wrap gap-6 mt-12 mb-6">
            <x-cta-button
                :label="$button['label']"
                :url="$button['url']"
                :style="$button['style']"
                :target="filled($button['target'] ?? null) ? $button['target'] : null"
            />
        </div>
    @endif
</div>
