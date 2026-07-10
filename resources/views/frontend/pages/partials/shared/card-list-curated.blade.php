@php
    /** @var array<int, array{title: string, url: string, target: string}> $cardItems */
    $sectionTitle = trim((string) ($sectionTitle ?? ''));
    $cardItems = $cardItems ?? [];
@endphp

@if(filled($sectionTitle) || $cardItems !== [])
    <div class="cms-card-list-curated">
        @if(filled($sectionTitle))
            <div class="cms-card-list-curated__heading w-full text-left">
                <h3 class="cms-section-title leading-tight tracking-[-.0253334em] mb-0 text-secondary">
                    {{ $sectionTitle }}
                </h3>
            </div>
        @endif

        @if($cardItems !== [])
            <div @class([
                'cms-card-list-curated__grid grid grid-cols-1 items-start gap-x-10 gap-y-12',
                'pt-10' => filled($sectionTitle),
            ])>
                @foreach ($cardItems as $cardItem)
                    @php
                        $url = trim((string) ($cardItem['url'] ?? ''));
                        $title = trim((string) ($cardItem['title'] ?? ''));
                        $target = (string) ($cardItem['target'] ?? '');
                        $isLink = filled($url);
                    @endphp

                    @if($isLink)
                        <a
                            href="{{ $url }}"
                            @if(filled($target)) target="{{ $target }}" @endif
                            @if($target === '_blank') rel="noopener noreferrer" @endif
                            class="cms-card-list-curated__item relative w-full rounded-lg p-8 flex flex-col self-stretch transition-all duration-300 items-start overflow-hidden border border-[#C6C6C6] group/noImageCard elevation-0 hover:elevation-3 hover:bg-grey-subtle"
                            style="--bg-color: #F2F2F2; background-color: var(--bg-color);"
                        >
                            @include('frontend.pages.partials.shared.card-list-item-inner', [
                                'title' => $title,
                                'showArrow' => true,
                            ])
                        </a>
                    @else
                        <div
                            class="cms-card-list-curated__item relative w-full rounded-lg p-8 flex flex-col self-stretch transition-all duration-300 items-start overflow-hidden border border-[#C6C6C6]"
                            style="--bg-color: #F2F2F2; background-color: var(--bg-color);"
                        >
                            @include('frontend.pages.partials.shared.card-list-item-inner', [
                                'title' => $title,
                                'showArrow' => false,
                            ])
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
@endif
