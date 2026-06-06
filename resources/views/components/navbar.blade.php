@props(['activePage' => 'Beranda', 'adminName' => 'Admin'])

<header class="flex items-center justify-between gap-4 min-h-[4rem] px-6 py-3 bg-white dark:bg-gray-900 border-b-2 border-primary-800 z-30 sticky top-0 transition-colors">
    <div class="flex items-center gap-3">
        @if(Auth::user() && Auth::user()->peran === 'admin')
            <!-- Mobile Menu Toggle Button -->
            <button type="button" onclick="toggleSidebar()" class="md:hidden p-1.5 rounded-xl text-primary-800 hover:bg-primary-50 hover:text-primary-950 transition-colors focus:outline-none cursor-pointer" aria-label="Open Menu">
                <x-heroicon-o-bars-3 class="w-6 h-6" />
            </button>
        @endif
        <div>
            <h1 class="text-lg font-extrabold text-primary-800 dark:text-primary-400 leading-none">{{ $activePage === 'Beranda' ? 'Ringkasan Beranda' : $activePage }}</h1>
        </div>
    </div>

    @php
    $user = Auth::user();
    $employee = $user ? \App\Models\Karyawan::where('badge', $user->badge)->first() : null;
    @endphp

    <!-- Right Profile Section with Dropdown -->
    <div class="flex items-center gap-2">
        <!-- Dark Mode Toggle -->
        <button type="button" onclick="toggleDarkMode()" class="p-2 text-gray-400 hover:text-primary-800 hover:bg-gray-100 rounded-xl transition-colors focus:outline-none" aria-label="Toggle dark mode" title="Toggle Theme">
            <x-heroicon-o-moon class="w-5 h-5 block dark:hidden" id="icon-moon" />
            <x-heroicon-o-sun class="w-5 h-5 hidden dark:block" id="icon-sun" />
        </button>

        <div class="relative inline-block text-left" id="profileDropdownContainer">
        <button type="button" onclick="toggleProfileDropdown()" class="flex items-center gap-3 text-sm font-bold text-gray-600 dark:text-gray-300 hover:text-primary-800 dark:hover:text-primary-400 transition-all focus:outline-none py-1.5 px-2.5 rounded-xl hover:bg-gray-50/80 dark:hover:bg-gray-800 active:scale-95 duration-200">
            <div class="w-11 h-11 rounded-full overflow-hidden flex-shrink-0 bg-gradient-to-br from-primary-800 to-primary-500 text-white font-extrabold shadow-md flex items-center justify-center border border-gray-100 dark:border-gray-800">
                @if($employee && $employee->foto)
                    <img src="{{ asset('storage/' . $employee->foto) }}" alt="Profile" class="w-full h-full object-cover">
                @else
                    <span>{{ strtoupper(substr($adminName, 0, 1)) }}</span>
                @endif
            </div>
            <span class="hidden sm:block text-left">
                <span class="block text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider leading-none mb-1">{{ Auth::user()->peran === 'admin' ? 'Administrator' : 'Karyawan' }}</span>
                <span class="block font-extrabold text-gray-800 dark:text-white leading-none">{{ $adminName }}</span>
            </span>
            <x-heroicon-o-chevron-down class="w-4 h-4 text-gray-400 transition-transform duration-300" id="profileChevron" />
        </button>

        <!-- Dropdown Menu -->
        <div id="profileDropdownMenu" class="absolute right-0 mt-2 w-48 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-700 shadow-xl py-2 hidden origin-top-right transform scale-95 opacity-0 transition-all duration-150 ease-out z-50">
            <!-- Signed in info for small screens -->
            <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700 sm:hidden">
                <p class="text-[0.65rem] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Masuk sebagai</p>
                <p class="text-sm font-bold text-gray-800 dark:text-white truncate">{{ $adminName }}</p>
            </div>
            
            <!-- Change Password Button -->
            <button type="button" onclick="openChangePasswordModal()" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all text-left">
                <x-heroicon-o-key class="w-4.5 h-4.5" />
                <span>Ubah Kata Sandi</span>
            </button>
            <hr class="border-gray-100 dark:border-gray-700 my-1">

            <form method="POST" action="{{ route('logout') }}" class="form-with-loading m-0">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm font-bold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all text-left">
                    <x-heroicon-o-arrow-right-on-rectangle class="w-4.5 h-4.5" />
                    <span class="btn-text">Keluar</span>
                    <svg class="btn-spinner animate-spin h-4 w-4 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>
        </div>
        </div>
    </div>
</header>

<script>
    // Initialize dark mode
    if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }

    function toggleDarkMode() {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('color-theme', 'light');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('color-theme', 'dark');
        }
    }

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
