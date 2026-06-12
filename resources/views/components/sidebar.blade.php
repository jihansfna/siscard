@php
$role = Auth::user()->peran ?? 'user';

$adminMenu = [
    ['name' => 'Beranda', 'href' => route('dashboard'), 'icon' => 'home', 'active' => 'dashboard'],
    ['name' => 'Master Karyawan', 'href' => route('dashboard.employees.index'), 'icon' => 'user-group', 'active' => 'dashboard.employees.*'],
    ['name' => 'Anggota', 'href' => route('dashboard.members'), 'icon' => 'users', 'active' => 'dashboard.members*'],
    ['name' => 'Saran', 'href' => route('dashboard.feedbacks'), 'icon' => 'chat-bubble-left-ellipsis', 'active' => 'dashboard.feedbacks*'],
    // ['name' => 'Riwayat', 'href' => route('dashboard.history'), 'icon' => 'clock', 'active' => 'dashboard.history*'],
];

$userMenu = [
    ['name' => 'Beranda', 'href' => route('user.home'), 'icon' => 'home', 'active' => 'user.home*'],
];

$menu = $role === 'admin' ? $adminMenu : $userMenu;
$sidebarLabel = $role === 'admin' ? 'Sistem Manajemen' : 'Area Anggota';
@endphp

<aside id="sidebarMenu" class="w-64 flex-shrink-0 flex flex-col border-r border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-xl shadow-gray-200/50 dark:shadow-none min-h-screen fixed inset-y-0 left-0 z-50 h-full transition-all duration-300 transform -translate-x-full md:translate-x-0">
    <div>
        <div class="px-5 pt-4 pb-0 md:hidden flex justify-end">
            <button type="button" onclick="toggleSidebar()" class="p-2 rounded-xl text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-red-600 dark:hover:text-red-400 transition-colors focus:outline-none cursor-pointer" aria-label="Close Sidebar">
                <x-heroicon-o-x-mark class="w-6 h-6" />
            </button>
        </div>
        <div class="border-b border-gray-100 dark:border-gray-800 px-6 pb-5 pt-2 md:pt-6">
            <h2 class="text-2xl font-bold text-primary-800 dark:text-primary-400 tracking-tight">SISCARD</h2>
            <span class="block mt-1 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $sidebarLabel }}</span>
        </div>

        <nav class="p-4 space-y-1">
            @foreach ($menu as $item)
                @php
                    $isActive = request()->routeIs($item['active']);
                @endphp
                <a href="{{ $item['href'] }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ $isActive ? 'bg-primary-800 text-white shadow-lg shadow-primary-800/30' : 'text-gray-600 hover:bg-primary-50 hover:text-primary-800' }}">
                    <x-dynamic-component :component="'heroicon-o-' . $item['icon']" class="w-5 h-5 flex-shrink-0" />
                    <span>{{ $item['name'] }}</span>
                </a>
            @endforeach
        </nav>
    </div>
</aside>
