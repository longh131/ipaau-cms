@php
    /** @var array{
     *     columns: array<int, array{
     *         title: string,
     *         content_html: string,
     *         button: ?array{label: string, url: string, style: string, target: string}
     *     }>
     * } $block */
    $columns = $block['columns'] ?? [];
    $visibleColumns = collect($columns)->filter(function (array $column): bool {
        return filled($column['title'] ?? null)
            || filled(strip_tags((string) ($column['content_html'] ?? '')))
            || filled($column['button'] ?? null);
    });
@endphp

@if($visibleColumns->isNotEmpty())
    <section
        data-type="basicContentWithColumns"
        class="cms-body-block cms-body-block--content-columns bg-[color:var(--bg-color)]"
        style="
            --bg-color: transparent;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
        "
    >
        <div class="inner container px-4 md:px-10 mx-auto px-7 flex flex-col gap-12">
            <div
                class="column-wrapper grid items-stretch text-left grid-cols-1 lg:grid-cols-2 [&>div:first-child]:border-l-0 [&>div:first-child]:border-t-0 [&>div]:max-lg:border-l-0 [&>*:nth-child(3n+3)]:lg:!border-l-0 [&>div:nth-child(2)]:xl:border-t-0 [&>*:nth-child(2n-3)]:lg:pr-5 [&>*:nth-child(2n-2)]:lg:pl-5 [&>*:nth-child(n+3)]:pt-5 [&>div]:border-[color:var(--ipa-border-color)]"
                style="--ipa-border-color: transparent; --ipa-border-style: solid"
            >
                @foreach ($visibleColumns as $column)
                    @include('frontend.pages.partials.body-blocks.partials.content-column', [
                        'column' => $column,
                    ])
                @endforeach
            </div>
        </div>
    </section>
@endif
