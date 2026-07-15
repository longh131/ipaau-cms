@if ($paginator->hasPages())
    <ul class="cms-pagination" role="list">
        @if ($paginator->onFirstPage())
            <li class="cms-pagination__item cms-pagination__item--disabled" aria-hidden="true">
                <span class="cms-pagination__link">上一页</span>
            </li>
        @else
            <li class="cms-pagination__item">
                <a class="cms-pagination__link" href="{{ $paginator->previousPageUrl() }}" rel="prev">上一页</a>
            </li>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="cms-pagination__item cms-pagination__item--ellipsis" aria-hidden="true">
                    <span class="cms-pagination__link">{{ $element }}</span>
                </li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="cms-pagination__item cms-pagination__item--active" aria-current="page">
                            <span class="cms-pagination__link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="cms-pagination__item">
                            <a class="cms-pagination__link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <li class="cms-pagination__item">
                <a class="cms-pagination__link" href="{{ $paginator->nextPageUrl() }}" rel="next">下一页</a>
            </li>
        @else
            <li class="cms-pagination__item cms-pagination__item--disabled" aria-hidden="true">
                <span class="cms-pagination__link">下一页</span>
            </li>
        @endif
    </ul>
@endif
