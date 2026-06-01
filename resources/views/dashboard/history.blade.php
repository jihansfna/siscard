<x-app-layout>
    <x-slot name="title">History</x-slot>

    <section class="bg-white dark:bg-[#1A1A1A] border border-blue-100 dark:border-none rounded-3xl p-6 shadow-sm dark:shadow-2xl transition-colors">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Activity History</h2>

            <div class="flex flex-wrap items-center gap-3">
                {{-- Search Form --}}
                <form id="searchForm" class="flex flex-wrap items-center gap-2" method="GET" action="{{ route('dashboard.history') }}">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                        </div>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search activity..." class="pl-10 pr-4 py-2 border border-gray-200 dark:border-gray-700/50 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50 dark:bg-[#242424] dark:text-white dark:placeholder-gray-500 transition-colors">
                    </div>
                </form>

                {{-- Filter Dropdown --}}
                <div class="relative" id="filterDropdownContainer">
                    <button type="button" onclick="toggleDropdown('filterDropdown')" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-[#242424] border border-gray-200 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-[#2A2A2A] text-gray-700 dark:text-gray-300 rounded-xl text-sm font-bold transition-all shadow-sm active:scale-95">
                        <x-heroicon-o-funnel class="w-4 h-4" />
                        <span>Filter</span>
                        @if(request('activity') || request('actor') || request('date'))
                            <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full border-2 border-white"></span>
                        @endif
                    </button>
                    <div id="filterDropdown" class="absolute right-0 mt-2 w-72 bg-white dark:bg-[#242424] rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/50 p-4 z-50 hidden transition-all">
                        <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700/50 pb-2 mb-4">
                            <h3 class="font-bold text-gray-800 dark:text-white">Filter History</h3>
                            <button type="button" onclick="toggleDropdown('filterDropdown')" class="text-gray-400 hover:text-gray-600">
                                <x-heroicon-o-x-mark class="w-5 h-5" />
                            </button>
                        </div>
                        <form method="GET" action="{{ route('dashboard.history') }}" class="space-y-4">
                            <!-- Preserve search query -->
                            <input type="hidden" name="q" value="{{ request('q') }}">
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Activity</label>
                                <div class="relative">
                                    <select name="activity" class="w-full pl-3 pr-8 py-2 border border-gray-200 dark:border-gray-700/50 rounded-lg text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-primary-500 bg-gray-50 dark:bg-[#1A1A1A] dark:text-white transition-colors">
                                        <option value="All Activity">All Activities</option>
                                        @foreach($activities as $act)
                                            <option value="{{ $act }}" {{ request('activity') == $act ? 'selected' : '' }}>{{ ucfirst($act) }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-gray-400">
                                        <x-heroicon-o-chevron-down class="w-4 h-4" />
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Actor</label>
                                <div class="relative">
                                    <select name="actor" class="w-full pl-3 pr-8 py-2 border border-gray-200 dark:border-gray-700/50 rounded-lg text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-primary-500 bg-gray-50 dark:bg-[#1A1A1A] dark:text-white transition-colors">
                                        <option value="All Actor">All Actors</option>
                                        @foreach($actors as $actr)
                                            <option value="{{ $actr->id }}" {{ request('actor') == $actr->id ? 'selected' : '' }}>{{ $actr->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-gray-400">
                                        <x-heroicon-o-chevron-down class="w-4 h-4" />
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Date</label>
                                <input type="date" name="date" value="{{ request('date') }}" class="w-full px-3 py-2 border border-gray-200 dark:border-gray-700/50 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-gray-50 dark:bg-[#1A1A1A] dark:text-white transition-colors">
                            </div>
                            
                            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                                <a href="{{ route('dashboard.history') }}" class="text-sm font-bold text-gray-500 hover:text-gray-700">Reset Filter</a>
                                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Export Dropdown --}}
                <div class="relative" id="exportDropdownContainer">
                    <button type="button" onclick="toggleDropdown('exportDropdown')" class="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-emerald-600/20 active:scale-95">
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                        <span>Export</span>
                        <x-heroicon-o-chevron-down class="w-3 h-3" />
                    </button>
                    <div id="exportDropdown" class="absolute right-0 mt-2 w-48 bg-white dark:bg-[#242424] rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/50 py-1 z-50 hidden transition-all">
                        <a href="{{ route('dashboard.export.history.excel') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-[#2A2A2A] transition-colors">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM9.5 11.5l2 3.5-2 3.5h1.5l1.25-2.5L13.5 18.5H15l-2-3.5 2-3.5h-1.5l-1.25 2.5-1.25-2.5H9.5z"/></svg>
                            <span>Export Excel</span>
                        </a>
                        <a href="{{ route('dashboard.export.history.pdf') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-[#2A2A2A] transition-colors">
                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM10.5 11c-.83 0-1.5.67-1.5 1.5v4c0 .83.67 1.5 1.5 1.5h3c.83 0 1.5-.67 1.5-1.5v-4c0-.83-.67-1.5-1.5-1.5h-3z"/></svg>
                            <span>Export PDF</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-[#242424] border border-gray-200 dark:border-gray-700/50 rounded-xl overflow-hidden shadow-md shadow-gray-200/60 dark:shadow-none transition-all">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-400">
                    <thead class="bg-gray-50 dark:bg-[#1A1A1A] text-gray-700 dark:text-gray-300 text-xs uppercase font-semibold border-b border-gray-200 dark:border-gray-700/50">
                        <tr>
                            <th class="px-4 py-3 w-10">No</th>
                            <th class="px-4 py-3">Time</th>
                            <th class="px-4 py-3">Actor</th>
                            <th class="px-4 py-3">Activity</th>
                            <th class="px-4 py-3">Target Member</th>
                            <th class="px-4 py-3">Description</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @forelse($logs as $index => $log)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-[#2A2A2A] transition-colors">
                                <td class="px-4 py-3">{{ $logs->firstItem() + $index }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $log->created_at->format('d M Y') }}</span>
                                        <span class="text-xs text-gray-500">{{ $log->created_at->format('H:i') }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">{{ $log->actor ? $log->actor->name : 'System' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-blue-50 text-blue-700 dark:bg-blue-900/25 dark:text-blue-300">
                                        {{ ucfirst($log->activity) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $log->member && $log->member->employee ? $log->member->employee->name : '-' }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $log->description ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <x-heroicon-o-clock class="w-10 h-10 text-gray-300 mb-3" />
                                        <p>No matching activity history found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($logs->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700/50 bg-gray-50 dark:bg-[#1A1A1A]">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </section>

    <script>
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const exportContainer = document.getElementById('exportDropdownContainer');
            if (exportContainer && !exportContainer.contains(e.target)) {
                document.getElementById('exportDropdown').classList.add('hidden');
            }
            
            const filterContainer = document.getElementById('filterDropdownContainer');
            if (filterContainer && !filterContainer.contains(e.target)) {
                document.getElementById('filterDropdown').classList.add('hidden');
            }
        });
    </script>
</x-app-layout>
