@if($hasImage ?? false)
    <div @class([
        'content-section content-section-1 content-section-1--image row-start-1',
        'col-start-1 lg:col-start-1' => $imageLeft,
        'col-start-1 lg:col-start-2' => ! $imageLeft,
    ])>
        <div @class([
            'about-cta__image',
            'about-cta__image--acorn' => ($imageShape ?? 'acorn') === 'acorn',
            'about-cta__image--rect' => ($imageShape ?? 'acorn') === 'rectangle',
        ])>
            <img
                src="{{ $block['image'] }}"
                alt="{{ $block['title'] ?? '' }}"
                loading="lazy"
            />
        </div>
    </div>
@endif
