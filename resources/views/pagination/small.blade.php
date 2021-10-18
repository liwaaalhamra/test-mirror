@if ($paginator->lastPage() > 1)
    <ul class="pagination pagination-sm m-t-none m-b-none">
        <li class="{{ ($paginator->currentPage() == 1) ? ' disabled' : '' }}">
            @if(!empty(request()->request->get('q')))
                <a href="{{ ($paginator->currentPage() == 1) ? '#' : $paginator->previousPageUrl().'&q='.request()->request->get('q') }}">Previous</a>
            @else
                <a href="{{ ($paginator->currentPage() == 1) ? '#' : $paginator->previousPageUrl() }}">Previous</a>
            @endif
        </li>
        @for ($i = 1; $i <= $paginator->lastPage(); $i++)
            <li class="{{ ($paginator->currentPage() == $i) ? ' active' : '' }}">
                @if(!empty(request()->request->get('q')))
                    <a href="{{ $paginator->url($i) }}&q={{request()->request->get('q')}}">{{ $i }}</a>
                @else
                    <a href="{{ $paginator->url($i) }}">{{ $i }}</a>
                @endif
            </li>
        @endfor

        <li class="{{ ($paginator->currentPage() == $paginator->lastPage()) ? ' disabled' : '' }}">
            @if(!empty(request()->request->get('q')))
                <a href="{{ ($paginator->currentPage() == $paginator->lastPage()) ? "#" : $paginator->url($paginator->currentPage()+1).'&q='.request()->request->get('q') }}" >Next</a>
            @else
                <a href="{{ ($paginator->currentPage() == $paginator->lastPage()) ? "#" : $paginator->url($paginator->currentPage()+1) }}" >Next</a>
            @endif
        </li>
    </ul>
@endif
