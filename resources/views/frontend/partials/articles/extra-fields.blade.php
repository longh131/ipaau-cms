@if($items !== [])
    <dl class="cms-article-extra-fields mt-8 pt-8 border-t border-grey-subtle grid gap-4 sm:grid-cols-2">
        @foreach ($items as $item)
            <div>
                <dt class="text-sm font-semibold text-secondary">{{ $item['label'] }}</dt>
                <dd class="mt-1 text-primary">
                    @switch($item['type'])
                        @case('url')
                            <a href="{{ $item['value'] }}" class="underline hover:no-underline" target="_blank" rel="noopener noreferrer">
                                {{ $item['value'] }}
                            </a>
                            @break
                        @case('image')
                            @php($imageUrl = \App\Support\ArticleExtraFields::assetUrl($item['value']))
                            @if($imageUrl)
                                <img src="{{ $imageUrl }}" alt="" class="max-w-full rounded-lg" loading="lazy" />
                            @endif
                            @break
                        @case('textarea')
                            <p class="whitespace-pre-line">{{ $item['value'] }}</p>
                            @break
                        @default
                            {{ $item['value'] }}
                    @endswitch
                </dd>
            </div>
        @endforeach
    </dl>
@endif
