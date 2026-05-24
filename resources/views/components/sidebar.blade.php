@php
$role = Auth::user()->role ?? 'user';

$adminMenu = [
    ['name' => 'Dashboard', 'href' => route('dashboard'), 'icon' => 'home'],
    ['name' => 'Master Employees', 'href' => route('dashboard.employees.index'), 'icon' => 'user-group'],
    ['name' => 'Members', 'href' => route('dashboard.members'), 'icon' => 'users'],
    ['name' => 'Feedbacks', 'href' => route('dashboard.feedbacks'), 'icon' => 'chat-bubble-left-ellipsis'],
    // ['name' => 'History', 'href' => route('dashboard.history'), 'icon' => 'clock'],
];

$userMenu = [
    ['name' => 'Beranda', 'href' => route('user.home'), 'icon' => 'home'],
];

$menu = $role === 'admin' ? $adminMenu : $userMenu;
$sidebarLabel = $role === 'admin' ? 'Dashboard Admin' : 'Member Area';
$currentUrl = request()->url();
@endphp

<aside id="sidebarMenu" class="w-64 flex-shrink-0 flex flex-col border-r border-gray-200 dark:border-gray-800 bg-white dark:bg-[#1A1A1A] shadow-xl shadow-gray-200/50 dark:shadow-none min-h-screen fixed inset-y-0 left-0 z-50 h-full transition-all duration-300 transform -translate-x-full md:translate-x-0">
    <div>
        <div class="border-b border-gray-100 dark:border-gray-800 p-6 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-primary-800 dark:text-primary-400 tracking-tight">SPSI</h2>
                <span class="block mt-1 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $sidebarLabel }}</span>
            </div>
            <!-- Mobile Close Button -->
            <button type="button" onclick="toggleSidebar()" class="md:hidden p-1.5 rounded-xl text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors focus:outline-none cursor-pointer" aria-label="Tutup Menu">
                <x-heroicon-o-x-mark class="w-6 h-6" />
            </button>
        </div>

        <nav class="p-4 space-y-1">
            @foreach ($menu as $item)
                @php
                    $isActive = $item['name'] === 'Dashboard' || $item['name'] === 'Beranda'
                        ? request()->url() === $item['href']
                        : str_starts_with($currentUrl, $item['href']);
                @endphp
                <a href="{{ $item['href'] }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 {{ $isActive ? 'bg-primary-800 text-white shadow-lg shadow-primary-800/30' : 'text-gray-600 hover:bg-primary-50 hover:text-primary-800' }}">
                    <x-dynamic-component :component="'heroicon-o-' . $item['icon']" class="w-5 h-5 flex-shrink-0" />
                    <span>{{ $item['name'] }}</span>
                </a>
            @endforeach
        </nav>
    </div>
</aside>
