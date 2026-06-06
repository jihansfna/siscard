@props(['paginator', 'perPageOptions' => [10, 25, 50, 100]])

@if ($paginator->hasPages() || $paginator->total() > 0)
<nav aria-label="Page navigation" class="flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4 mt-1">
    {{-- Info text --}}
    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">
        Showing <span class="font-bold text-gray-700 dark:text-gray-200">{{ $paginator->firstItem() ?? 0 }}</span>
        to <span class="font-bold text-gray-700 dark:text-gray-200">{{ $paginator->lastItem() ?? 0 }}</span>
        of <span class="font-bold text-gray-700 dark:text-gray-200">{{ $paginator->total() }}</span> results
    </p>

    <div class="flex items-center gap-3">
        {{-- Page numbers --}}
        @if ($paginator->hasPages())
        <ul class="inline-flex -space-x-px text-xs shadow-sm rounded-md">
            {{-- Previous --}}
            <li>
                @if ($paginator->onFirstPage())
                    <span class="flex items-center justify-center px-2.5 h-8 text-gray-400 dark:text-gray-600 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-l-md font-medium cursor-not-allowed select-none">Previous</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="flex items-center justify-center px-2.5 h-8 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-l-md font-medium hover:bg-gray-100 dark:hover:bg-[#2A2A2A] hover:text-gray-800 dark:hover:text-white transition-colors">Previous</a>
                @endif
            </li>

            {{-- Page Numbers --}}
            @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                @if ($page == $paginator->currentPage())
                    <li>
                        <span aria-current="page" class="flex items-center justify-center w-8 h-8 text-white border border-primary-600 bg-primary-600 font-bold">{{ $page }}</span>
                    </li>
                @else
                    <li>
                        <a href="{{ $url }}" class="flex items-center justify-center w-8 h-8 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 font-medium hover:bg-gray-100 dark:hover:bg-[#2A2A2A] hover:text-gray-800 dark:hover:text-white transition-colors">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            {{-- Next --}}
            <li>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="flex items-center justify-center px-2.5 h-8 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-r-md font-medium hover:bg-gray-100 dark:hover:bg-[#2A2A2A] hover:text-gray-800 dark:hover:text-white transition-colors">Next</a>
                @else
                    <span class="flex items-center justify-center px-2.5 h-8 text-gray-400 dark:text-gray-600 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-r-md font-medium cursor-not-allowed select-none">Next</span>
                @endif
            </li>
        </ul>
        @endif

        {{-- Per page dropdown --}}
        <div class="relative custom-dropdown-container">
            <input type="hidden" id="perPageInput_{{ $paginator->getPageName() }}" value="{{ request('perPage', $paginator->perPage()) }}">
            <button type="button" onclick="toggleCustomDropdown('perPageMenu_{{ $paginator->getPageName() }}')" class="flex items-center justify-between gap-2 px-2.5 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 text-xs rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 shadow-sm transition-colors cursor-pointer min-w-[95px] font-medium hover:bg-gray-50 dark:hover:bg-[#2A2A2A]">
                <span id="perPageBtnText_{{ $paginator->getPageName() }}">{{ request('perPage', $paginator->perPage()) }} per page</span>
                <svg class="w-3.5 h-3.5 text-gray-400 pointer-events-none flex-shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/></svg>
            </button>
            <div id="perPageMenu_{{ $paginator->getPageName() }}" class="custom-dropdown-menu absolute z-50 hidden bottom-full mb-1.5 right-0 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg">
                <ul class="py-1 text-xs text-gray-700 dark:text-gray-300 font-medium">
                    @foreach($perPageOptions as $option)
                        <li>
                            <button type="button" onclick="changePerPage({{ $option }})" class="inline-flex items-center w-full px-2.5 py-1.5 hover:bg-gray-100 dark:hover:bg-[#2A2A2A] transition-colors {{ request('perPage', $paginator->perPage()) == $option ? 'text-primary-600 dark:text-primary-500 font-bold bg-primary-50 dark:bg-primary-900/20' : '' }}">{{ $option }} per page</button>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</nav>

<script>
    function changePerPage(value) {
        const url = new URL(window.location.href);
        url.searchParams.set('perPage', value);
        url.searchParams.delete('page');
        window.location.href = url.toString();
    }
</script>
@endif
