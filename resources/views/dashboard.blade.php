<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="grid gap-6">
        <!-- Stats Grid -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6" aria-label="Statistik utama">
            <!-- Empty state stats or real ones when available -->
            <article class="bg-white border border-blue-100 rounded-2xl p-6 shadow-sm hover:shadow-xl hover:shadow-primary-900/10 transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-500">Total Anggota</p>
                        <strong class="block mt-1 text-3xl font-extrabold text-primary-800 leading-tight">0</strong>
                    </div>
                    <div class="grid place-items-center w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex-shrink-0">
                        <x-heroicon-o-users class="w-6 h-6" />
                    </div>
                </div>
            </article>

            <article class="bg-white border border-blue-100 rounded-2xl p-6 shadow-sm hover:shadow-xl hover:shadow-primary-900/10 transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-500">Feedback Masuk</p>
                        <strong class="block mt-1 text-3xl font-extrabold text-primary-800 leading-tight">0</strong>
                    </div>
                    <div class="grid place-items-center w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex-shrink-0">
                        <x-heroicon-o-chat-bubble-left-ellipsis class="w-6 h-6" />
                    </div>
                </div>
            </article>

            <article class="bg-white border border-blue-100 rounded-2xl p-6 shadow-sm hover:shadow-xl hover:shadow-primary-900/10 transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-500">Tingkat Respons</p>
                        <strong class="block mt-1 text-3xl font-extrabold text-primary-800 leading-tight">0%</strong>
                    </div>
                    <div class="grid place-items-center w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex-shrink-0">
                        <x-heroicon-o-chart-bar class="w-6 h-6" />
                    </div>
                </div>
            </article>
        </section>

        <!-- Empty State Panel -->
        <section class="bg-white border border-blue-100 rounded-2xl p-8 shadow-sm flex flex-col items-center justify-center min-h-[400px] text-center">
            <div class="w-24 h-24 mb-6 rounded-full bg-blue-50 grid place-items-center text-blue-500">
                <x-heroicon-o-chart-pie class="w-12 h-12" />
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Belum ada data tersedia</h2>
            <p class="text-gray-500 max-w-md">Data statistik anggota dan feedback akan muncul di sini setelah ada aktivitas yang terekam dalam sistem.</p>
        </section>
    </div>
</x-app-layout>
