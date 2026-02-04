<style>
    .button {
        background: rgb(0, 87, 95);
        color: white;
        border-radius: 0;
        padding: 6px 16px;
    }

    .list-item {
        padding: 5px 15px;
        margin: 0 5px;
        text-align: center;
    }

    .link{
        color: black;
    }

    .link:hover{
        text-decoration: underline;
        color: black;
    }

    .button:hover{
        text-decoration: underline;
        color: white;
    }

    .active{
        color: rgb(0, 87, 95);
        border: 1px solid rgb(0, 87, 95);
    }

</style>

@if ($paginator->hasPages())
    <ul class="pagination mt-4" role="navigation">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="d-flex align-items-center disabled" aria-disabled="true"
                aria-label="@lang('pagination.previous')">
                <span class="button" aria-hidden="true">&lsaquo;</span>
            </li>
        @else
            <li class="d-flex align-items-center">
                <a class="button" href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   aria-label="@lang('pagination.previous')">&lsaquo;</a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li  class="disabled list-item" aria-disabled="true"><span class="link">{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="list-item active" aria-current="page"><span class="link">{{ $page }}</span></li>
                    @else
                        <li class="list-item"><a class="link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="d-flex align-items-center">
                <a class="button" href="{{ $paginator->nextPageUrl() }}" rel="next"
                   aria-label="@lang('pagination.next')">&rsaquo;</a>
            </li>
        @else
            <li class="d-flex align-items-center disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                <span class="button" aria-hidden="true">&rsaquo;</span>
            </li>
        @endif
    </ul>
@endif
