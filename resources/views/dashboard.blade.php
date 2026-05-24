<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Dashboard Container with dynamic theme -->
    <div class="bg-white dark:bg-[#1A1A1A] border border-gray-100 dark:border-none rounded-3xl p-8 shadow-sm dark:shadow-2xl text-gray-800 dark:text-gray-200 transition-colors">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">Dashboard</h1>
            <p class="text-gray-500 dark:text-gray-400">Selamat datang kembali, {{ auth()->user()->name }}</p>
        </div>

        <!-- Ringkasan -->
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Ringkasan</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Card 1 -->
            <div class="bg-gray-50 dark:bg-[#242424] border border-gray-100 dark:border-gray-700/50 rounded-2xl p-5 flex items-center gap-5 hover:bg-gray-100 dark:hover:bg-[#2A2A2A] transition-colors">
                <div class="w-14 h-14 rounded-2xl bg-[#F4F0FF] flex items-center justify-center flex-shrink-0">
                    <x-heroicon-o-users class="w-7 h-7 text-[#7C3AED]" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Total anggota</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white leading-tight">{{ $totalAnggota }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">+{{ $totalAnggotaThisMonth }} bulan ini</p>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="bg-gray-50 dark:bg-[#242424] border border-gray-100 dark:border-gray-700/50 rounded-2xl p-5 flex items-center gap-5 hover:bg-gray-100 dark:hover:bg-[#2A2A2A] transition-colors">
                <div class="w-14 h-14 rounded-2xl bg-[#F0FDF4] flex items-center justify-center flex-shrink-0">
                    <x-heroicon-o-arrow-down-tray class="w-7 h-7 text-[#16A34A]" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium leading-tight mb-1">Download kartu hari ini</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white leading-tight">{{ $downloadsToday }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $downloadsDiffText }}</p>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="bg-gray-50 dark:bg-[#242424] border border-gray-100 dark:border-gray-700/50 rounded-2xl p-5 flex items-center gap-5 hover:bg-gray-100 dark:hover:bg-[#2A2A2A] transition-colors">
                <div class="w-14 h-14 rounded-2xl bg-[#FFFBEB] flex items-center justify-center flex-shrink-0">
                    <x-heroicon-o-clock class="w-7 h-7 text-[#D97706]" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mb-1">Pending konfirmasi</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white leading-tight">{{ $pendingMembers }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Menunggu persetujuan</p>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="bg-gray-50 dark:bg-[#242424] border border-gray-100 dark:border-gray-700/50 rounded-2xl p-5 flex items-center gap-5 hover:bg-gray-100 dark:hover:bg-[#2A2A2A] transition-colors">
                <div class="w-14 h-14 rounded-2xl bg-[#FEF2F2] flex items-center justify-center flex-shrink-0">
                    <x-heroicon-o-chat-bubble-left-ellipsis class="w-7 h-7 text-[#DC2626]" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mb-1">Saran masuk</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white leading-tight">{{ $totalFeedbacks }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $pendingFeedbacks }} belum ditangani</p>
                </div>
            </div>
        </div>

        <!-- Grafik & Statistik -->
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Grafik & Statistik</h2>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            
            <!-- Bar Chart -->
            <div class="bg-gray-50 dark:bg-[#242424] border border-gray-100 dark:border-gray-700/50 rounded-2xl p-6 lg:col-span-2 flex flex-col">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-gray-900 dark:text-white font-bold text-lg mb-1">Scan barcode per hari</h3>
                        <div class="flex items-center gap-4 text-xs font-medium">
                            <div class="flex items-center gap-1.5">
                                <div class="w-3 h-3 bg-[#3B82F6] rounded-[3px]"></div>
                                <span class="text-gray-500 dark:text-gray-400">Jumlah scan</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <div class="w-3 h-0.5 bg-[#10B981]"></div>
                                <span class="text-gray-500 dark:text-gray-400">Rata-rata</span>
                            </div>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-gray-500 dark:text-gray-400">{{ $chartData['monthName'] }}</span>
                </div>
                <div class="flex-1 w-full relative min-h-[250px]">
                    <canvas id="scanChart"></canvas>
                </div>
            </div>

            <!-- Donut Chart -->
            <div class="bg-gray-50 dark:bg-[#242424] border border-gray-100 dark:border-gray-700/50 rounded-2xl p-6 flex flex-col">
                <h3 class="text-gray-900 dark:text-white font-bold text-lg mb-4">Status anggota</h3>
                <div class="flex items-center gap-4 text-xs font-medium mb-6 flex-wrap justify-center">
                    <div class="flex items-center gap-1.5">
                        <div class="w-3 h-3 bg-[#65A30D] rounded-[3px]"></div>
                        <span class="text-gray-500 dark:text-gray-400">Aktif {{ $donutData['aktif'] }}%</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-3 h-3 bg-[#D97706] rounded-[3px]"></div>
                        <span class="text-gray-500 dark:text-gray-400">Tidak aktif {{ $donutData['tidakAktif'] }}%</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-3 h-3 bg-[#D1D5DB] rounded-[3px]"></div>
                        <span class="text-gray-500 dark:text-gray-400">Keluar {{ $donutData['keluar'] }}%</span>
                    </div>
                </div>
                <div class="flex-1 w-full relative flex items-center justify-center min-h-[200px]">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    <!-- Chart Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Setup Bar Chart (Scan Barcode)
            const ctxScan = document.getElementById('scanChart').getContext('2d');
            
            // Chart Data from Backend
            const labels = {!! json_encode($chartData['labels']) !!};
            const data = {!! json_encode($chartData['data']) !!};
            const average = {{ $chartData['average'] }};

            new Chart(ctxScan, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Scan',
                        data: data,
                        backgroundColor: '#3B82F6',
                        borderRadius: 4,
                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1F2937',
                            titleColor: '#F3F4F6',
                            bodyColor: '#F3F4F6',
                            borderColor: '#374151',
                            borderWidth: 1,
                            padding: 10,
                            displayColors: false,
                            callbacks: {
                                title: function(context) {
                                    return context[0].label + ' {{ $chartData['monthName'] }}';
                                }
                            }
                        },
                        annotation: {
                            // Can add chartjs-plugin-annotation for the average line if needed
                            // For now we'll draw it natively or rely on chart features
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#374151',
                                drawBorder: false,
                            },
                            ticks: {
                                color: '#9CA3AF',
                                font: { size: 11 }
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false,
                            },
                            ticks: {
                                color: '#9CA3AF',
                                font: { size: 11 }
                            }
                        }
                    }
                },
                plugins: [{
                    id: 'averageLine',
                    afterDraw: function(chart) {
                        if (average > 0) {
                            var ctx = chart.ctx;
                            var xAxis = chart.scales.x;
                            var yAxis = chart.scales.y;
                            var y = yAxis.getPixelForValue(average);

                            ctx.save();
                            ctx.beginPath();
                            ctx.moveTo(xAxis.left, y);
                            ctx.lineTo(xAxis.right, y);
                            ctx.lineWidth = 2;
                            ctx.strokeStyle = '#10B981';
                            ctx.setLineDash([5, 5]);
                            ctx.stroke();
                            ctx.restore();
                        }
                    }
                }]
            });

            // Setup Donut Chart (Status Anggota)
            const ctxStatus = document.getElementById('statusChart').getContext('2d');
            const donutData = {!! json_encode(array_values($donutData)) !!};

            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: ['Aktif', 'Tidak Aktif', 'Keluar'],
                    datasets: [{
                        data: donutData,
                        backgroundColor: [
                            '#65A30D', // Aktif - Green
                            '#D97706', // Tidak Aktif - Orange
                            '#D1D5DB'  // Keluar - Gray
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1F2937',
                            titleColor: '#F3F4F6',
                            bodyColor: '#F3F4F6',
                            borderColor: '#374151',
                            borderWidth: 1,
                            padding: 10,
                            callbacks: {
                                label: function(context) {
                                    return ' ' + context.label + ': ' + context.parsed + '%';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
