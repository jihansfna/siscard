<x-app-layout>
    <x-slot name="title">History</x-slot>

    <section class="bg-white border border-blue-100 rounded-2xl p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <h2 class="text-xl font-bold text-gray-800">Riwayat Aktivitas</h2>
        </div>

        <!-- Empty State Table -->
        <div class="border border-gray-200 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-gray-700 text-xs uppercase font-semibold border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 w-10">No</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Aktivitas</th>
                            <th class="px-4 py-3">Aktor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <x-heroicon-o-clock class="w-10 h-10 text-gray-300 mb-3" />
                                    <p>Belum ada riwayat aktivitas.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</x-app-layout>
