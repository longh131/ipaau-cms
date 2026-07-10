@php
    /** @var array{title: string, image: ?string, url: string, target: string} $card */
    $hasImage = filled($card['image'] ?? null);
    $hasLink = filled($card['url'] ?? null);
    $title = trim((string) ($card['title'] ?? ''));
    $cellClass = match ($bentoStyle ?? 'five') {
        'tall' => match ($index) {
            0 => 'cms-governance-bento__cell cms-governance-bento__cell--tall-0',
            1 => 'cms-governance-bento__cell cms-governance-bento__cell--tall-1',
            2 => 'cms-governance-bento__cell cms-governance-bento__cell--tall-2',
            3 => 'cms-governance-bento__cell cms-governance-bento__cell--tall-3',
            default => 'cms-governance-bento__cell',
        },
        'wide' => match ($index) {
            0 => 'cms-governance-bento__cell cms-governance-bento__cell--wide-0',
            1 => 'cms-governance-bento__cell cms-governance-bento__cell--wide-1',
            2 => 'cms-governance-bento__cell cms-governance-bento__cell--wide-2',
            3 => 'cms-governance-bento__cell cms-governance-bento__cell--wide-3',
            default => 'cms-governance-bento__cell',
        },
        default => match ($index) {
            0 => 'cms-governance-bento__cell cms-governance-bento__cell--five-0',
            1 => 'cms-governance-bento__cell cms-governance-bento__cell--five-1',
            2 => 'cms-governance-bento__cell cms-governance-bento__cell--five-2',
            3 => 'cms-governance-bento__cell cms-governance-bento__cell--five-3',
            4 => 'cms-governance-bento__cell cms-governance-bento__cell--five-4',
            default => 'cms-governance-bento__cell',
        },
    };
@endphp

<div class="{{ $cellClass }}">
    <div @class([
        'cms-governance-bento__card relative overflow-hidden h-full w-full rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300',
        'cms-governance-bento__card--image' => $hasImage,
        'cms-governance-bento__card--plain' => ! $hasImage,
    ])>
        @if($hasImage)
            <div class="absolute inset-0">
                <img
                    src="{{ $card['image'] }}"
                    alt=""
                    class="w-full h-full object-cover"
                    loading="lazy"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
            </div>
        @endif

        @if(filled($title))
            <div class="relative z-10 h-full flex flex-col justify-end break-all p-6">
                @if($hasLink)
                    <h3 class="label-xl mb-0 text-white">
                        <a
                            href="{{ $card['url'] }}"
                            @if(filled($card['target'] ?? null)) target="{{ $card['target'] }}" @endif
                            @if(($card['target'] ?? '') === '_blank') rel="noopener noreferrer" @endif
                            class="cms-governance-bento__link flex items-center justify-between w-full gap-2 group/bentoBoxLink text-white"
                        >
                            <span class="line-clamp-3 group-hover/bentoBoxLink:underline">{{ $title }}</span>
                            <span class="shrink-0 group-hover/bentoBoxLink:translate-x-2 transition-transform duration-300">
                                @include('partials.icons.ipa-link-arrow', [
                                    'variant' => 'dark',
                                ])
                            </span>
                        </a>
                    </h3>
                @else
                    <h3 class="label-xl mb-0 text-white">{{ $title }}</h3>
                @endif
            </div>
        @endif
    </div>
</div>
