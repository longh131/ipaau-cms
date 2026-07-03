@if(! empty($block['buttons'] ?? []))
    <div class="cms-body-block cms-body-block--cta component-cta flex flex-col shrink-0 sm:flex-row flex-wrap gap-4 justify-center lg:justify-start">
        @foreach ($block['buttons'] as $button)
            <x-cta-button
                :label="$button['label']"
                :url="$button['url']"
                :style="$button['style']"
                :target="filled($button['target'] ?? null) ? $button['target'] : null"
            />
        @endforeach
    </div>
@endif
