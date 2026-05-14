@php
$role = Auth::user()->role ?? 'user';

$adminMenu = [
    ['name' => 'Dashboard', 'href' => route('dashboard'), 'icon' => 'home'],
    ['name' => 'Master Employees', 'href' => route('dashboard.employees.index'), 'icon' => 'user-group'],
    ['name' => 'Members', 'href' => route('dashboard.members'), 'icon' => 'users'],
    ['name' => 'Feedbacks', 'href' => route('dashboard.feedbacks'), 'icon' => 'chat-bubble-left-ellipsis'],
    ['name' => 'History', 'href' => route('dashboard.history'), 'icon' => 'clock'],
];

$userMenu = [
    ['name' => 'Beranda', 'href' => route('user.home'), 'icon' => 'home'],
];

$menu = $role === 'admin' ? $adminMenu : $userMenu;
$sidebarLabel = $role === 'admin' ? 'Dashboard Admin' : 'Member Area';
$currentUrl = request()->url();
@endphp

<aside class="w-64 flex-shrink-0 flex flex-col justify-between border-r border-gray-200 bg-white shadow-xl shadow-gray-200/50 hidden md:flex min-h-screen fixed z-20 h-full">
    <div>
        <div class="border-b border-gray-100 p-6">
            <h2 class="text-2xl font-bold text-primary-800 tracking-tight">SPSI</h2>
            <span class="block mt-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $sidebarLabel }}</span>
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

    <div class="border-t border-gray-100 p-4">
        {{-- Role indicator --}}
        <div class="mb-3 px-4 py-2 rounded-xl bg-gray-50 border border-gray-100">
            <p class="text-[0.65rem] font-bold text-gray-400 uppercase tracking-wider">Role</p>
            <p class="text-sm font-bold {{ $role === 'admin' ? 'text-primary-700' : 'text-green-700' }}">
                {{ $role === 'admin' ? 'Administrator' : 'Employee' }}
            </p>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="form-with-loading">
            @csrf
            <button type="submit" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium text-gray-600 transition-all duration-200 hover:bg-red-50 hover:text-red-600">
                <div class="flex items-center gap-3">
                    <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5 flex-shrink-0" />
                    <span class="btn-text">Logout</span>
                </div>
                <svg class="btn-spinner animate-spin h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </form>
    </div>
</aside>
