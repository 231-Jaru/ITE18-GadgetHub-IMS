@php
    // Check if it's a paginator instance
    $isPaginator = method_exists($paginator, 'hasPages') && method_exists($paginator, 'currentPage');
@endphp

@if ($isPaginator && $paginator->hasPages())
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $onEachSide = 2; // Show 2 pages on each side of current page
        $start = max(1, $currentPage - $onEachSide);
        $end = min($lastPage, $currentPage + $onEachSide);
    @endphp
    
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center mb-0">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">
                        <i class="fas fa-chevron-left"></i> Previous
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                </li>
            @endif

            {{-- First Page --}}
            @if ($start > 1)
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url(1) }}">1</a>
                </li>
                @if ($start > 2)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
            @endif

            {{-- Page Numbers --}}
            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $currentPage)
                    <li class="page-item active">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                    </li>
                @endif
            @endfor

            {{-- Last Page --}}
            @if ($end < $lastPage)
                @if ($end < $lastPage - 1)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url($lastPage) }}">{{ $lastPage }}</a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">
                        Next <i class="fas fa-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
        
        {{-- Simple Info Text --}}
        <div class="text-center mt-2 text-muted small">
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
        </div>
    </nav>
@endif

<style>
    .pagination {
        gap: 0.25rem;
    }
    
    .pagination .page-link {
        border-radius: 6px;
        border: 1px solid #dee2e6;
        padding: 0.5rem 0.75rem;
        color: #495057;
        transition: all 0.2s ease;
    }
    
    .pagination .page-link:hover {
        background-color: #e9ecef;
        border-color: #adb5bd;
        transform: translateY(-1px);
    }
    
    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
        font-weight: 600;
    }
    
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #f8f9fa;
        cursor: not-allowed;
        opacity: 0.6;
    }
</style>

