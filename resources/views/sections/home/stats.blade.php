@php
    /** @var array{items: array<int, array{number_type: string, number: string, number_image: ?string, title: string, content: string}>} $stats */
    $stats = $stats ?? ['items' => []];
    $itemCount = count($stats['items']);
    $gridClass = match (true) {
        $itemCount >= 4 => 'grid-cols-2 lg:grid-cols-4',
        $itemCount === 3 => 'grid-cols-2 lg:grid-cols-3',
        default => 'grid-cols-2',
    };
@endphp
@if(! empty($stats['items']))
        <section
          data-type="statsCards"
          data-index="2"
          class="py-12 bg-[color:var(--bg-color)]"
          style="
            --bg-color: transparent;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
          "
        >
          <div
            class="inner container px-4 md:px-10 mx-auto flex flex-col gap-12"
          >
            <div class="container mx-auto px-4">
              <div @class(['grid gap-4 lg:gap-10', $gridClass])>
                @foreach ($stats['items'] as $index => $item)
                <x-stat-card
                  :number-type="$item['number_type'] ?? 'text'"
                  :number="$item['number'] ?? ''"
                  :number-image="$item['number_image'] ?? null"
                  :title="$item['title'] ?? ''"
                  :content="$item['content'] ?? ''"
                  :wide-on-mobile="$index === 2 && $itemCount === 3"
                />
                @endforeach
              </div>
            </div>
          </div>
        </section>
@endif
