@props(['activePage' => 'Dashboard', 'adminName' => 'Admin'])

<header class="flex items-center justify-between gap-4 min-h-[4rem] px-6 py-3 bg-white border-b-2 border-primary-800 shadow-xl shadow-primary-900/10 z-10 sticky top-0">
    <div>
        <span class="block text-[0.7rem] font-extrabold text-gray-500 uppercase tracking-widest">SISCARD</span>
        <h1 class="mt-0.5 text-lg font-extrabold text-primary-800 leading-tight">{{ $activePage }}</h1>
    </div>

    <div class="flex items-center gap-3">
        <div class="flex items-center gap-3 text-sm font-bold text-gray-600">
            <div class="grid place-items-center w-9 h-9 rounded-full bg-gradient-to-br from-primary-800 to-primary-500 text-white font-extrabold shadow-md">
                {{ strtoupper(substr($adminName, 0, 1)) }}
            </div>
            <span class="hidden sm:block">{{ $adminName }}</span>
        </div>
        
        <!-- Mobile menu toggle button could go here -->
    </div>
</header>
