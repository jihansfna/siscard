<x-app-layout>
    <x-slot name="title">Beranda</x-slot>

    <!-- ApexCharts CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- Dashboard Container -->
    <div class="text-gray-800 dark:text-gray-200 transition-colors">

        <h2 class="text-xs font-bold tracking-widest text-gray-400 dark:text-gray-500 uppercase mb-4">Ringkasan</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">

            {{-- Card 1 Total Anggota Aktif --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-5 hover:shadow-lg dark:hover:shadow-gray-900/40 transition-all duration-300 shadow-sm dark:shadow-none group">
                <div class="flex items-start gap-4">
                    <div class="w-11 h-11 rounded-xl bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                        <x-heroicon-o-users class="w-5 h-5 text-violet-600 dark:text-violet-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium leading-tight">Total Anggota Aktif</p>
                        <p class="text-3xl font-extrabold text-gray-900 dark:text-white leading-tight mt-1">{{ number_format($totalActiveMembers, 0, ',', '.') }}</p>
                        <span class="inline-flex items-center mt-1.5 text-[11px] font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 rounded-md">
                            +{{ $totalActiveMembersThisMonth }} bulan ini
                        </span>
                    </div>
                </div>
            </div>



            {{-- Card 3 Pending Verifikasi --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-5 hover:shadow-lg dark:hover:shadow-gray-900/40 transition-all duration-300 shadow-sm dark:shadow-none group">
                <div class="flex items-start gap-4">
                    <div class="w-11 h-11 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                        <x-heroicon-o-clock class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium leading-tight">Pending Verifikasi</p>
                        <p class="text-3xl font-extrabold text-gray-900 dark:text-white leading-tight mt-1">{{ $pendingMembers }}</p>
                        <span class="inline-flex items-center mt-1.5 text-[11px] font-bold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-2 py-0.5 rounded-md">
                            Rata-rata {{ $avgVerificationDays }} hari
                        </span>
                    </div>
                </div>
            </div>

            {{-- Card 4 Aduan Belum Selesai --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-5 hover:shadow-lg dark:hover:shadow-gray-900/40 transition-all duration-300 shadow-sm dark:shadow-none group">
                <div class="flex items-start gap-4">
                    <div class="w-11 h-11 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                        <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-600 dark:text-red-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium leading-tight">Aduan Belum Selesai</p>
                        <p class="text-3xl font-extrabold text-gray-900 dark:text-white leading-tight mt-1">{{ $pendingFeedbacks }}</p>
                        @if($pendingFeedbacks > 0)
                        <span class="inline-flex items-center mt-1.5 text-[11px] font-bold text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/30 px-2 py-0.5 rounded-md">
                            Perlu ditangani
                        </span>
                        @else
                        <span class="inline-flex items-center mt-1.5 text-[11px] font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 rounded-md">
                            Semua selesai
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <h2 class="text-xs font-bold tracking-widest text-gray-400 dark:text-gray-500 uppercase mb-4">Grafik & Statistik</h2>

        <div class="grid grid-cols-1 xl:grid-cols-5 gap-4">

            <div class="xl:col-span-3 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-5 md:p-6 shadow-sm dark:shadow-none flex flex-col">
                {{-- Header --}}
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-2">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Pertumbuhan Anggota</h3>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Anggota baru vs keluar per bulan</p>
                    </div>
                    <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 whitespace-nowrap">{{ $growthPeriodLabel }}</span>
                </div>
                {{-- Legend --}}
                <div class="flex items-center gap-4 text-xs font-medium mb-4">
                    <div class="flex items-center gap-1.5">
                        <div class="w-3 h-3 bg-violet-500 rounded-sm"></div>
                        <span class="text-gray-500 dark:text-gray-400">Anggota baru</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-3 h-3 bg-red-500 rounded-sm"></div>
                        <span class="text-gray-500 dark:text-gray-400">Keluar / nonaktif</span>
                    </div>
                </div>
                {{-- Chart --}}
                <div class="flex-1 min-h-[280px]">
                    <div id="growthChart" class="w-full h-full"></div>
                </div>
            </div>

            <div class="xl:col-span-2 flex flex-col gap-4">

                {{-- Aktivitas Kartu Digital (Line Chart) --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-5 md:p-6 shadow-sm dark:shadow-none flex flex-col flex-1">
                    <div class="mb-2">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Aktivitas Kartu Digital</h3>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Download & scan 30 hari terakhir</p>
                    </div>
                    <div class="flex items-center gap-4 text-xs font-medium mb-3">
                        <div class="flex items-center gap-1.5">
                            <div class="w-3 h-3 bg-blue-500 rounded-sm"></div>
                            <span class="text-gray-500 dark:text-gray-400">Download kartu</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div class="w-3 h-0.5 border-t-2 border-dashed border-emerald-500"></div>
                            <span class="text-gray-500 dark:text-gray-400">Scan QR</span>
                        </div>
                    </div>
                    <div class="flex-1 min-h-[180px]">
                        <div id="activityChart" class="w-full h-full"></div>
                    </div>
                </div>

                {{-- Status Anggota (Progress Bars) --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-5 md:p-6 shadow-sm dark:shadow-none">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Status Anggota</h3>
                    <div class="space-y-4">
                        {{-- Aktif --}}
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Aktif</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $activePercent }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-blue-500 to-blue-400 h-2.5 rounded-full transition-all duration-1000 ease-out" style="width: {{ $activePercent }}%"></div>
                            </div>
                        </div>
                        {{-- Tidak Aktif --}}
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Tidak Aktif</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $inactivePercent }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-amber-500 to-amber-400 h-2.5 rounded-full transition-all duration-1000 ease-out" style="width: {{ $inactivePercent }}%"></div>
                            </div>
                        </div>
                        {{-- Keluar --}}
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Keluar</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $exitedPercent }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-red-500 to-red-400 h-2.5 rounded-full transition-all duration-1000 ease-out" style="width: {{ $exitedPercent }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Detect dark mode
            const isDark = document.documentElement.classList.contains('dark');

            const textColor   = isDark ? '#9CA3AF' : '#6B7280';
            const gridColor   = isDark ? '#374151' : '#F3F4F6';
            const tooltipBg   = isDark ? '#1F2937' : '#FFFFFF';
            const tooltipText = isDark ? '#F3F4F6' : '#1F2937';

            const growthOptions = {
                series: [{
                    name: 'Anggota baru',
                    data: {!! json_encode($growthNew) !!}
                }, {
                    name: 'Keluar / nonaktif',
                    data: {!! json_encode($growthExited) !!}
                }],
                chart: {
                    type: 'bar',
                    height: '100%',
                    fontFamily: 'Inter, system-ui, sans-serif',
                    toolbar: { show: false },
                    background: 'transparent',
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                        animateGradually: { enabled: true, delay: 150 },
                        dynamicAnimation: { enabled: true, speed: 350 }
                    }
                },
                colors: ['#8B5CF6', '#EF4444'],
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        borderRadiusApplication: 'end',
                        columnWidth: '55%',
                        dataLabels: { position: 'top' }
                    }
                },
                dataLabels: { enabled: false },
                stroke: { show: true, width: 2, colors: ['transparent'] },
                xaxis: {
                    categories: {!! json_encode($growthLabels) !!},
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        style: { colors: textColor, fontSize: '12px', fontWeight: 500 }
                    }
                },
                yaxis: {
                    labels: {
                        style: { colors: textColor, fontSize: '11px' },
                        formatter: function (val) { return Math.round(val); }
                    }
                },
                grid: {
                    borderColor: gridColor,
                    strokeDashArray: 4,
                    xaxis: { lines: { show: false } },
                    yaxis: { lines: { show: true } },
                    padding: { top: -10, bottom: 0 }
                },
                tooltip: {
                    theme: isDark ? 'dark' : 'light',
                    y: { formatter: function (val) { return val + ' anggota'; } }
                },
                legend: { show: false }
            };

            const growthChart = new ApexCharts(document.querySelector('#growthChart'), growthOptions);
            growthChart.render();

            const activityOptions = {
                series: [{
                    name: 'Download kartu',
                    data: {!! json_encode($activityDownloads) !!}
                }, {
                    name: 'Scan QR',
                    data: {!! json_encode($activityScans) !!}
                }],
                chart: {
                    type: 'line',
                    height: '100%',
                    fontFamily: 'Inter, system-ui, sans-serif',
                    toolbar: { show: false },
                    background: 'transparent',
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 1000,
                    },
                    zoom: { enabled: false }
                },
                colors: ['#3B82F6', '#10B981'],
                stroke: {
                    width: [2.5, 2.5],
                    curve: 'smooth',
                    dashArray: [0, 5]
                },
                markers: { size: 0, hover: { size: 5 } },
                xaxis: {
                    categories: {!! json_encode($activityLabels) !!},
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        style: { colors: textColor, fontSize: '10px' },
                        rotate: 0,
                        hideOverlappingLabels: true,
                        showDuplicates: false,
                    },
                    tickAmount: 6
                },
                yaxis: {
                    labels: {
                        style: { colors: textColor, fontSize: '11px' },
                        formatter: function (val) { return Math.round(val); }
                    }
                },
                grid: {
                    borderColor: gridColor,
                    strokeDashArray: 4,
                    xaxis: { lines: { show: false } },
                    yaxis: { lines: { show: true } },
                    padding: { top: -10, bottom: 0 }
                },
                tooltip: {
                    theme: isDark ? 'dark' : 'light',
                    x: { formatter: function (val) { return 'Hari ke-' + val; } },
                    y: { formatter: function (val) { return val + ' kali'; } }
                },
                legend: { show: false }
            };

            const activityChart = new ApexCharts(document.querySelector('#activityChart'), activityOptions);
            activityChart.render();

            const progressBars = document.querySelectorAll('[style*="width:"]');
            progressBars.forEach(function(bar) {
                const target = bar.style.width;
                bar.style.width = '0%';
                setTimeout(function() {
                    bar.style.width = target;
                }, 300);
            });
        });
    </script>
</x-app-layout>
