@if(filled($block['text'] ?? null))
    @php
        $highlightText = (string) $block['text'];
        $isHtml = str_contains($highlightText, '<');
        $gradientClass = $block['gradient_class'] ?? 'text-gradient-purple-reverse';
    @endphp
    <div class="cms-body-block cms-body-block--highlight about-rich-text text-center">
        <div class="cms-highlight-html text-display-md lg:text-display-lg mb-0 text-secondary">
            @if ($isHtml)
                {!! $highlightText !!}
            @else
                <span class="{{ $gradientClass }}">{{ $highlightText }}</span>
            @endif
        </div>
    </div>
@endif
