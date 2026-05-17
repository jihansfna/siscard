@props(['activePage' => 'Dashboard', 'adminName' => 'Admin'])

<header class="flex items-center justify-between gap-4 min-h-[4rem] px-6 py-3 bg-white border-b-2 border-primary-800 shadow-xl shadow-primary-900/10 z-30 sticky top-0">
    <div>
        <span class="block text-[0.7rem] font-extrabold text-gray-500 uppercase tracking-widest">SISCARD</span>
        <h1 class="mt-0.5 text-lg font-extrabold text-primary-800 leading-tight">{{ $activePage }}</h1>
    </div>

    <!-- Right Profile Section with Dropdown -->
    <div class="relative inline-block text-left" id="profileDropdownContainer">
        <button type="button" onclick="toggleProfileDropdown()" class="flex items-center gap-3 text-sm font-bold text-gray-600 hover:text-primary-800 transition-all focus:outline-none py-1.5 px-2.5 rounded-xl hover:bg-gray-50/80 active:scale-95 duration-200">
            <div class="grid place-items-center w-9 h-9 rounded-full bg-gradient-to-br from-primary-800 to-primary-500 text-white font-extrabold shadow-md">
                {{ strtoupper(substr($adminName, 0, 1)) }}
            </div>
            <span class="hidden sm:block text-left">
                <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider leading-none mb-1">{{ Auth::user()->role === 'admin' ? 'Administrator' : 'Employee' }}</span>
                <span class="block font-extrabold text-gray-800 leading-none">{{ $adminName }}</span>
            </span>
            <x-heroicon-o-chevron-down class="w-4 h-4 text-gray-400 transition-transform duration-300" id="profileChevron" />
        </button>

        <!-- Dropdown Menu -->
        <div id="profileDropdownMenu" class="absolute right-0 mt-2 w-48 rounded-2xl bg-white border border-gray-100 shadow-xl py-2 hidden origin-top-right transform scale-95 opacity-0 transition-all duration-150 ease-out z-50">
            <!-- Signed in info for small screens -->
            <div class="px-4 py-2 border-b border-gray-100 sm:hidden">
                <p class="text-[0.65rem] font-bold text-gray-400 uppercase tracking-wider">Signed in as</p>
                <p class="text-sm font-bold text-gray-800 truncate">{{ $adminName }}</p>
            </div>
            


            <form method="POST" action="{{ route('logout') }}" class="form-with-loading m-0">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50 transition-all text-left">
                    <x-heroicon-o-arrow-right-on-rectangle class="w-4.5 h-4.5" />
                    <span class="btn-text">Logout</span>
                    <svg class="btn-spinner animate-spin h-4 w-4 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</header>

<script>
    function toggleProfileDropdown() {
        const dropdown = document.getElementById('profileDropdownMenu');
        const chevron = document.getElementById('profileChevron');
        
        if (dropdown.classList.contains('hidden')) {
            dropdown.classList.remove('hidden');
            setTimeout(() => {
                dropdown.classList.remove('scale-95', 'opacity-0');
                dropdown.classList.add('scale-100', 'opacity-100');
            }, 10);
            chevron.classList.add('rotate-180');
        } else {
            closeProfileDropdown();
        }
    }

    function closeProfileDropdown() {
        const dropdown = document.getElementById('profileDropdownMenu');
        const chevron = document.getElementById('profileChevron');
        
        if (dropdown && !dropdown.classList.contains('hidden')) {
            dropdown.classList.remove('scale-100', 'opacity-100');
            dropdown.classList.add('scale-95', 'opacity-0');
            chevron.classList.remove('rotate-180');
            setTimeout(() => {
                dropdown.classList.add('hidden');
            }, 150);
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const container = document.getElementById('profileDropdownContainer');
        if (container && !container.contains(event.target)) {
            closeProfileDropdown();
        }
    });
</script>
