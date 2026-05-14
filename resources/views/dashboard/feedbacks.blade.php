<x-app-layout>
    <x-slot name="title">Manajemen Saran & Masukan</x-slot>

    <section class="bg-white border border-blue-100 rounded-2xl p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <h2 class="text-xl font-bold text-gray-800">Manajemen Saran & Masukan</h2>

            <div class="flex flex-wrap items-center gap-3">
                <form class="flex items-center gap-2" method="GET" action="{{ route('dashboard.feedbacks') }}">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                        </div>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau badge..." class="pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50">
                    </div>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-semibold flex items-center gap-3">
                <x-heroicon-s-check-circle class="w-5 h-5 flex-shrink-0" />
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-semibold flex items-start gap-3">
                <x-heroicon-s-exclamation-circle class="w-5 h-5 flex-shrink-0 mt-0.5" />
                <div class="space-y-1">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="border border-gray-200 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-gray-700 text-xs uppercase font-semibold border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 w-10">No</th>
                            <th class="px-4 py-3">Pengirim</th>
                            <th class="px-4 py-3">Isi Saran</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($feedbacks as $index => $fb)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3 text-gray-500">{{ $feedbacks->firstItem() + $index }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-gray-800">{{ $fb->member->employee->name ?? 'Unknown' }}</div>
                                    <div class="text-xs text-gray-500">{{ $fb->member->employee->badge ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 max-w-xs truncate" title="{{ $fb->description }}">
                                    {{ $fb->description }}
                                    @if($fb->remark)
                                        <div class="text-xs text-primary-600 mt-1 font-medium truncate" title="Balasan: {{ $fb->remark }}">Balasan: {{ $fb->remark }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $fb->created_at->format('d M Y, H:i') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($fb->status === 'Completed')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Completed</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Waiting</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($fb->status === 'Waiting')
                                        <button type="button" title="Mark As Completed" onclick="openCompleteModal('{{ $fb->id }}')" class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors">
                                            <x-heroicon-o-check-circle class="w-5 h-5" />
                                        </button>
                                    @else
                                        <button type="button" title="Telah Diselesaikan" disabled class="p-1.5 text-green-500 opacity-50 cursor-not-allowed">
                                            <x-heroicon-s-check-circle class="w-5 h-5" />
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <x-heroicon-o-chat-bubble-left-ellipsis class="w-10 h-10 text-gray-300 mb-3" />
                                        <p>Belum ada feedback dari anggota.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($feedbacks->hasPages())
                <div class="p-4 border-t border-gray-100 bg-gray-50">
                    {{ $feedbacks->links() }}
                </div>
            @endif
        </div>
    </section>

    <!-- Complete Feedback Modal -->
    <div id="completeFeedbackModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Tandai Sebagai Selesai</h3>
                <button type="button" onclick="closeCompleteModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
            
            <form id="completeFeedbackForm" action="" method="POST" class="form-with-loading">
                @csrf
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-200 text-sm text-yellow-800 mb-4">
                            Silakan isi balasan / remark untuk saran ini. Balasan akan dikirimkan ke pengirim.
                        </div>
                        <div>
                            <label for="remark" class="block text-sm font-bold text-gray-700 mb-1">Remark / Balasan <span class="text-red-500">*</span></label>
                            <textarea id="remark" name="remark" rows="3" required placeholder="Saran telah kami terima dan akan segera ditindaklanjuti..." class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm resize-none"></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                    <button type="button" onclick="closeCompleteModal()" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-200 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 text-sm font-bold bg-green-600 hover:bg-green-700 text-white rounded-xl shadow-lg shadow-green-600/20 transition-all flex items-center gap-2">
                        <span class="btn-text">Kirim Remark & Selesai</span>
                        <svg class="btn-spinner animate-spin h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCompleteModal(id) {
            const form = document.getElementById('completeFeedbackForm');
            form.action = `/dashboard/feedbacks/${id}/complete`;
            document.getElementById('completeFeedbackModal').classList.remove('hidden');
            document.getElementById('completeFeedbackModal').classList.add('flex');
        }

        function closeCompleteModal() {
            document.getElementById('completeFeedbackModal').classList.add('hidden');
            document.getElementById('completeFeedbackModal').classList.remove('flex');
        }
    </script>
</x-app-layout>
