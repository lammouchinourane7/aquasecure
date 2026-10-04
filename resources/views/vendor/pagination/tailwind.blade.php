@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between">
        <p class="text-sm text-muted">
            @if ($paginator->firstItem())
                {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} sur {{ $paginator->total() }}
            @else
                {{ $paginator->count() }} résultat(s)
            @endif
        </p>

        <div class="flex items-center gap-1 text-sm">
            @if ($paginator->onFirstPage())
                <span class="px-2.5 py-1 text-muted/50 cursor-not-allowed">&larr; Précédent</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-2.5 py-1 text-muted hover:text-ink transition">&larr; Précédent</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 text-muted/60">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="px-2.5 py-1 rounded-lg bg-primary/10 font-semibold text-primary-strong">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-2.5 py-1 rounded-lg text-muted hover:text-ink hover:bg-bg transition">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-2.5 py-1 text-muted hover:text-ink transition">Suivant &rarr;</a>
            @else
                <span class="px-2.5 py-1 text-muted/50 cursor-not-allowed">Suivant &rarr;</span>
            @endif
        </div>
    </nav>
@endif
