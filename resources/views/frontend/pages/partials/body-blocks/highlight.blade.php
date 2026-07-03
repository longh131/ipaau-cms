@if(filled($block['text'] ?? null))
    <div class="cms-body-block cms-body-block--highlight about-rich-text text-center">
        <p class="text-display-md lg:text-display-lg mb-0">
            <span class="{{ $block['gradient_class'] ?? 'text-gradient-purple-reverse' }}">
                {{ $block['text'] }}
            </span>
        </p>
    </div>
@endif
