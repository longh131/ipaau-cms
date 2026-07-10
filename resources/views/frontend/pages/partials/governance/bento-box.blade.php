@php
    /** @var array<int, array{title: string, image: ?string, url: string, target: string}> $bentoCards */
    $bentoStyle = $bentoStyle ?? 'five';
    $cardsPerBlock = in_array($bentoStyle, ['tall', 'wide'], true) ? 4 : 5;
    $groups = array_chunk($bentoCards, $cardsPerBlock);
    $gridClass = match ($bentoStyle) {
        'tall' => 'cms-governance-bento__grid cms-governance-bento__grid--tall',
        'wide' => 'cms-governance-bento__grid cms-governance-bento__grid--wide',
        default => 'cms-governance-bento__grid cms-governance-bento__grid--five',
    };
@endphp

<section
    data-type="bentoBox"
    data-index="1"
    class="py-16 cms-governance-module cms-governance-bento"
>
    <div class="inner container px-4 md:px-10 mx-auto">
        @foreach ($groups as $groupIndex => $group)
            <div @class(['cms-governance-bento__block', 'mt-10 sm:mt-16' => $groupIndex > 0])>
                <div class="{{ $gridClass }}">
                    @foreach ($group as $index => $card)
                        @include('frontend.pages.partials.governance.partials.bento-card', [
                            'card' => $card,
                            'index' => $index,
                            'bentoStyle' => $bentoStyle,
                        ])
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</section>
