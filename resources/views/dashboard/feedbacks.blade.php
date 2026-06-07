<x-app-layout>
    <x-slot name="title">Manajemen Tanggapan</x-slot>

    <section class="text-gray-800 dark:text-gray-200 transition-colors">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Manajemen Tanggapan</h2>

            <div class="flex flex-wrap items-center gap-3">
                <form class="flex items-center gap-2" method="GET" action="{{ route('dashboard.feedbacks') }}">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                        </div>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau badge..." class="pl-10 pr-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500 transition-colors">
                        @if(request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        @if(request('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif
                        @if(request('perPage'))
                            <input type="hidden" name="perPage" value="{{ request('perPage') }}">
                        @endif
                    </div>
                </form>

                <button type="button" onclick="openFilterModal()" class="flex items-center gap-2 px-4 py-2 border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-[#2A2A2A] text-gray-700 dark:text-gray-300 rounded-lg text-sm font-semibold transition-all shadow-sm active:scale-95">
                    <x-heroicon-o-funnel class="w-4 h-4" />
                    <span>Filter</span>
                </button>

                {{-- Export Dropdown --}}
                <div class="relative" id="exportDropdownContainer">
                    <button type="button" onclick="toggleDropdown('exportDropdown')" class="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-emerald-600/20 active:scale-95">
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                        <span>Ekspor</span>
                        <x-heroicon-o-chevron-down class="w-3 h-3" />
                    </button>
                    <div id="exportDropdown" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 py-1 z-50 hidden transition-all">
                        <a href="{{ route('dashboard.export.feedbacks.excel') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-[#2A2A2A] transition-colors">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM9.5 11.5l2 3.5-2 3.5h1.5l1.25-2.5L13.5 18.5H15l-2-3.5 2-3.5h-1.5l-1.25 2.5-1.25-2.5H9.5z"/></svg>
                            <span>Ekspor Excel</span>
                        </a>
                        <a href="{{ route('dashboard.export.feedbacks.pdf') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-[#2A2A2A] transition-colors">
                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM10.5 11c-.83 0-1.5.67-1.5 1.5v4c0 .83.67 1.5 1.5 1.5h3c.83 0 1.5-.67 1.5-1.5v-4c0-.83-.67-1.5-1.5-1.5h-3z"/></svg>
                            <span>Ekspor PDF</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

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

        <!-- Bulk Delete Actions Bar -->
        <div id="bulkDeleteBar" class="hidden items-center justify-between bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800/50 rounded-xl p-4 mb-4 transition-all duration-300">
            <div class="flex items-center gap-2 text-red-700 dark:text-red-400 text-sm font-semibold">
                <x-heroicon-o-trash class="w-5 h-5 text-red-500" />
                <span id="selectedCount">0</span> data terpilih
            </div>
            <button type="submit" form="bulkDeleteForm" class="flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-bold transition-all shadow-md shadow-red-600/20 active:scale-95">
                <span>Hapus Terpilih</span>
            </button>
        </div>

        <form id="bulkDeleteForm" action="{{ route('dashboard.feedbacks.bulk_destroy') }}" method="POST" class="hidden">
            @csrf
        </form>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-md shadow-gray-200/60 dark:shadow-none transition-all">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-400">
                    <thead class="bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300 text-xs uppercase font-semibold border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-4 py-3 w-10">
                                <x-checkbox id="selectAllFeedbacks" onclick="toggleSelectAllFeedbacks(this)" />
                            </th>
                            <th class="px-4 py-3 w-10">No</th>
                            <th class="px-4 py-3">Pengirim</th>
                            <th class="px-4 py-3">Deskripsi</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @forelse($feedbacks as $index => $fb)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-[#2A2A2A] transition-colors">
                                <td class="px-4 py-3">
                                    <x-checkbox name="ids[]" value="{{ $fb->id }}" form="bulkDeleteForm" class="feedback-checkbox" onclick="updateBulkDeleteBar()" />
                                </td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $feedbacks->firstItem() + $index }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-gray-800 dark:text-white">{{ $fb->anggota->karyawan->nama ?? 'Unknown' }}</div>
                                    <div class="text-xs text-gray-500">{{ $fb->anggota->karyawan->badge ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 max-w-xs truncate" title="{{ $fb->deskripsi }}">
                                    {{ $fb->deskripsi }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $fb->created_at->format('d M Y, H:i') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($fb->status === 'Completed')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Selesai</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">Menunggu</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-1">
                                        @php
                                            $feedbackData = [
                                                'id' => $fb->id,
                                                'name' => $fb->anggota->karyawan->nama ?? 'Unknown',
                                                'badge' => $fb->anggota->karyawan->badge ?? '-',
                                                'description' => $fb->deskripsi,
                                                'file' => $fb->berkas,
                                                'created_at' => $fb->created_at->format('d F Y, H:i'),
                                                'status' => $fb->status,
                                                'remark' => $fb->catatan ?? '',
                                                'image' => $fb->anggota->karyawan->foto ?? null,
                                            ];
                                        @endphp
                                        <button type="button" title="Lihat Detail" data-feedback="{{ json_encode($feedbackData) }}" onclick="openFeedbackDetail(this)" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors cursor-pointer">
                                            <x-heroicon-o-eye class="w-5 h-5" />
                                        </button>
                                        
                                        @if($fb->status === 'Waiting')
                                            <button type="button" title="Tandai Selesai" onclick="openCompleteModal('{{ $fb->id }}')" class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors cursor-pointer">
                                                <x-heroicon-o-check-circle class="w-5 h-5" />
                                            </button>
                                        @else
                                            <button type="button" title="Selesai" disabled class="p-1.5 text-green-500 opacity-50 cursor-not-allowed">
                                                <x-heroicon-s-check-circle class="w-5 h-5" />
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <x-heroicon-o-chat-bubble-left-ellipsis class="w-10 h-10 text-gray-300 mb-3" />
                                        <p>Belum ada tanggapan dari anggota.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($feedbacks->hasPages() || $feedbacks->total() > 0)
                <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                    <x-custom-pagination :paginator="$feedbacks" />
                </div>
            @endif
        </div>
    </section>

    <!-- Complete Feedback Modal -->
    <div id="completeFeedbackModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/50 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col border border-transparent dark:border-gray-700">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Tandai Selesai</h3>
                <button type="button" onclick="closeCompleteModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
            
            <form id="completeFeedbackForm" action="" method="POST" class="form-with-loading">
                @csrf
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-200 text-sm text-yellow-800 mb-4">
                            Silakan isi balasan / catatan untuk tanggapan ini. Balasan akan dikirim ke pengirim.
                        </div>
                        <div>
                            <label for="catatan" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Catatan / Balasan <span class="text-red-500">*</span></label>
                            <textarea id="catatan" name="catatan" rows="3" required placeholder="Isi tanggapan anda" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm resize-none text-gray-900 dark:text-white placeholder:text-gray-400 placeholder:italic dark:placeholder:text-gray-500"></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex justify-end gap-3">
                    <button type="button" onclick="closeCompleteModal()" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-200 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 text-sm font-bold bg-green-600 hover:bg-green-700 text-white rounded-xl shadow-lg shadow-green-600/20 transition-all flex items-center gap-2">
                        <span class="btn-text">Kirim Catatan & Selesai</span>
                        <svg class="btn-spinner animate-spin h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Feedback Detail Off-Canvas Drawer -->
    <div id="feedbackDetailDrawer" onclick="if(event.target === this) closeFeedbackDetail()" class="fixed inset-0 z-50 hidden bg-gray-900/50 backdrop-blur-sm transition-opacity">
        <div class="fixed inset-y-0 right-0 w-full max-w-xl bg-white dark:bg-gray-800 shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col border-l border-transparent dark:border-gray-700" id="drawerContent">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-white dark:bg-gray-800 z-10 sticky top-0">
                <div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white leading-none">Tanggapan dan Saran</h3>
                    <div class="mt-2.5">
                        <span id="detailStatusBadge" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"></span>
                    </div>
                </div>
                <button type="button" onclick="closeFeedbackDetail()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors absolute top-4 right-4 cursor-pointer">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
            
            <!-- Body -->
            <div class="p-6 overflow-y-auto flex-1 bg-gray-50/50 dark:bg-gray-900 space-y-5">
                <!-- Sender Info Section (Clean & Card-less) -->
                <div class="flex items-center gap-3.5 pb-4 border-b border-gray-200/80 dark:border-gray-700">
                    <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0 shadow-sm border border-gray-200 bg-primary-800 text-white flex items-center justify-center">
                        <img id="detailSenderImage" src="" alt="Profile" class="w-full h-full object-cover hidden">
                        <div id="detailSenderFallback" class="w-full h-full flex items-center justify-center font-bold text-sm">
                            -
                        </div>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Pengirim</p>
                        <h5 class="font-bold text-gray-800 dark:text-white text-sm leading-tight mt-0.5" id="detailSenderName">-</h5>
                        <p class="text-[11px] text-gray-500 font-semibold mt-0.5" id="detailSenderBadge">-</p>
                    </div>
                </div>

                <!-- Feedback Details (Clean & Card-less) -->
                <div class="space-y-5">
                    <div>
                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Tanggal Dikirim</p>
                        <p class="font-bold text-gray-800 dark:text-white text-sm" id="detailDate">-</p>
                    </div>
                    
                    <div>
                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Deskripsi Tanggapan</p>
                        <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed whitespace-pre-wrap font-normal" id="detailDescription">-</p>
                    </div>

                    <!-- File / Document Attached -->
                    <div id="detailFileContainer" class="hidden pt-1">
                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-2">Dokumen / Berkas</p>
                        <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <x-heroicon-o-document-text class="w-5.5 h-5.5 text-primary-600 flex-shrink-0" />
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-gray-800 dark:text-white truncate" id="detailFileName">-</p>
                                    <p class="text-[10px] text-gray-400 font-semibold leading-none mt-0.5">Lampiran berkas dari anggota</p>
                                </div>
                            </div>
                            <a id="detailDownloadLink" href="" download class="flex-shrink-0 flex items-center gap-1.5 px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-xs font-bold transition-all cursor-pointer">
                                <span>Unduh</span>
                                <x-heroicon-o-arrow-down-tray class="w-3.5 h-3.5" />
                            </a>
                        </div>
                        <!-- File Preview if Image -->
                        <div id="detailImagePreviewContainer" class="mt-3 hidden">
                            <p class="text-[10px] font-semibold text-gray-400 mb-1.5">Pratinjau Gambar:</p>
                            <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden max-h-48 bg-white dark:bg-gray-900 flex items-center justify-center p-2">
                                <img id="detailImagePreview" src="" alt="Preview" class="max-w-full max-h-44 object-contain rounded">
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200 dark:border-gray-700">

                <!-- Admin Response Remark Section (Clean & Card-less) -->
                <div id="detailResponseContainer" class="space-y-3">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider" id="detailResponseTitle">Balasan HRD</p>
                    
                    <!-- If already completed -->
                    <div id="completedResponseArea" class="hidden">
                        <div class="p-4 bg-primary-50/40 dark:bg-primary-900/20 border border-primary-100/80 dark:border-primary-700/30 rounded-2xl shadow-sm">
                            <div class="flex items-start gap-3">
                                <div class="p-1.5 bg-primary-100 dark:bg-primary-800/40 rounded-xl text-primary-700 dark:text-primary-400 flex-shrink-0">
                                    <x-heroicon-s-chat-bubble-left-ellipsis class="w-4 h-4" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-gray-700 dark:text-gray-200 text-sm leading-relaxed whitespace-pre-wrap font-medium" id="detailRemarkText">-</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- If waiting, show placeholder info -->
                    <div id="waitingResponseArea" class="hidden">
                        <div class="p-4 bg-amber-50/30 dark:bg-amber-900/20 border border-amber-100/80 dark:border-amber-700/30 rounded-2xl shadow-sm flex items-start gap-3">
                            <div class="p-1.5 bg-amber-100 dark:bg-amber-800/40 rounded-xl text-amber-700 dark:text-amber-400 flex-shrink-0">
                                <x-heroicon-o-exclamation-triangle class="w-4 h-4" />
                            </div>
                            <p class="text-amber-800 dark:text-amber-300 text-xs font-semibold leading-relaxed">Belum ada respons. Silakan gunakan tombol centang (✓) di kolom aksi tabel untuk memberikan respons dan menyelesaikan keluhan ini.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div id="filterModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/50 backdrop-blur-sm p-4 transition-opacity">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-sm flex flex-col border border-transparent dark:border-gray-700">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Filter Tanggapan</h3>
                <button type="button" onclick="closeFilterModal()" class="text-gray-400 hover:text-gray-600 transition-colors cursor-pointer">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
            
            <form action="{{ route('dashboard.feedbacks') }}" method="GET" class="m-0">
                <!-- Keep search query if it exists -->
                @if(request('q'))
                    <input type="hidden" name="q" value="{{ request('q') }}">
                @endif
                @if(request('perPage'))
                    <input type="hidden" name="perPage" value="{{ request('perPage') }}">
                @endif
                
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <div class="relative custom-dropdown-container">
                            <input type="hidden" name="status" id="fbStatusInput" value="{{ request('status', 'Semua Status') }}">
                            <button type="button" onclick="toggleCustomDropdown('fbStatusMenu')" class="w-full flex items-center justify-between pl-4 pr-3 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors text-gray-700 dark:text-white cursor-pointer shadow-sm">
                                <span id="fbStatusBtnText">{{ request('status', 'Semua Status') }}</span>
                                <svg class="w-4 h-4 text-gray-400 pointer-events-none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/></svg>
                            </button>
                            <div id="fbStatusMenu" class="custom-dropdown-menu absolute z-50 hidden mt-1.5 w-full bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-lg">
                                <ul class="py-1 text-sm text-gray-700 dark:text-gray-300 font-medium">
                                    <li><button type="button" onclick="selectDropdownOption('fbStatusInput', 'fbStatusBtnText', 'fbStatusMenu', 'Semua Status', 'Semua Status')" class="inline-flex items-center w-full px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-[#2A2A2A] transition-colors">Semua Status</button></li>
                                    <li><button type="button" onclick="selectDropdownOption('fbStatusInput', 'fbStatusBtnText', 'fbStatusMenu', 'Waiting', 'Menunggu')" class="inline-flex items-center w-full px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-[#2A2A2A] transition-colors">Menunggu</button></li>
                                    <li><button type="button" onclick="selectDropdownOption('fbStatusInput', 'fbStatusBtnText', 'fbStatusMenu', 'Completed', 'Selesai')" class="inline-flex items-center w-full px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-[#2A2A2A] transition-colors">Selesai</button></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Urutkan Berdasarkan</label>
                        <div class="relative custom-dropdown-container">
                            <input type="hidden" name="sort" id="fbSortInput" value="{{ request('sort', 'desc') }}">
                            <button type="button" onclick="toggleCustomDropdown('fbSortMenu')" class="w-full flex items-center justify-between pl-4 pr-3 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors text-gray-700 dark:text-white cursor-pointer shadow-sm">
                                <span id="fbSortBtnText">{{ request('sort', 'desc') === 'desc' ? 'Data Terbaru' : 'Data Terlama' }}</span>
                                <svg class="w-4 h-4 text-gray-400 pointer-events-none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/></svg>
                            </button>
                            <div id="fbSortMenu" class="custom-dropdown-menu absolute z-50 hidden mt-1.5 w-full bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-lg">
                                <ul class="py-1 text-sm text-gray-700 dark:text-gray-300 font-medium">
                                    <li><button type="button" onclick="selectDropdownOption('fbSortInput', 'fbSortBtnText', 'fbSortMenu', 'desc', 'Data Terbaru')" class="inline-flex items-center w-full px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-[#2A2A2A] transition-colors">Data Terbaru</button></li>
                                    <li><button type="button" onclick="selectDropdownOption('fbSortInput', 'fbSortBtnText', 'fbSortMenu', 'asc', 'Data Terlama')" class="inline-flex items-center w-full px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-[#2A2A2A] transition-colors">Data Terlama</button></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 flex items-center justify-between border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-b-2xl">
                    <a href="{{ route('dashboard.feedbacks') }}{{ request('q') ? '?q=' . request('q') : '' }}" class="text-sm font-bold text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                        Reset Filter
                    </a>
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-primary-600/20 active:scale-95 cursor-pointer">
                        Kirim
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openFilterModal() {
            document.getElementById('filterModal').classList.remove('hidden');
            document.getElementById('filterModal').classList.add('flex');
        }

        function closeFilterModal() {
            document.getElementById('filterModal').classList.add('hidden');
            document.getElementById('filterModal').classList.remove('flex');
        }

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

        function openFeedbackDetail(btn) {
            const feedback = JSON.parse(btn.dataset.feedback);
            
            // Set sender info
            document.getElementById('detailSenderName').textContent = feedback.name;
            document.getElementById('detailSenderBadge').textContent = 'Badge: ' + feedback.badge;
            
            // Set sender image/fallback
            const senderImage = document.getElementById('detailSenderImage');
            const senderFallback = document.getElementById('detailSenderFallback');
            if (feedback.image) {
                senderImage.src = '/storage/' + feedback.image;
                senderImage.classList.remove('hidden');
                senderFallback.classList.add('hidden');
            } else {
                senderImage.classList.add('hidden');
                senderFallback.classList.remove('hidden');
                senderFallback.textContent = feedback.name.charAt(0).toUpperCase();
            }
            
            // Set date and description
            document.getElementById('detailDate').textContent = feedback.created_at;
            document.getElementById('detailDescription').textContent = feedback.description;
            
            // Set status badge
            const statusBadge = document.getElementById('detailStatusBadge');
            if (feedback.status === 'Completed') {
                statusBadge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
                statusBadge.textContent = 'Selesai';
            } else {
                statusBadge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
                statusBadge.textContent = 'Menunggu';
            }
            
            // File Handling
            const fileContainer = document.getElementById('detailFileContainer');
            const imagePreviewContainer = document.getElementById('detailImagePreviewContainer');
            
            if (feedback.file) {
                fileContainer.classList.remove('hidden');
                
                // Get filename from path
                const fileName = feedback.file.split('/').pop();
                document.getElementById('detailFileName').textContent = fileName;
                
                const fileUrl = '/storage/' + feedback.file;
                document.getElementById('detailDownloadLink').href = fileUrl;
                
                // Check if file is image
                const ext = fileName.split('.').pop().toLowerCase();
                if (['jpg', 'jpeg', 'png'].includes(ext)) {
                    document.getElementById('detailImagePreview').src = fileUrl;
                    imagePreviewContainer.classList.remove('hidden');
                } else {
                    imagePreviewContainer.classList.add('hidden');
                }
            } else {
                fileContainer.classList.add('hidden');
                imagePreviewContainer.classList.add('hidden');
            }
            
            // Response Area
            const completedArea = document.getElementById('completedResponseArea');
            const waitingArea = document.getElementById('waitingResponseArea');
            const responseContainer = document.getElementById('detailResponseContainer');
            
            document.getElementById('detailResponseTitle').textContent = 'Balasan HRD';
            if (feedback.status === 'Completed') {
                responseContainer.classList.remove('hidden');
                completedArea.classList.remove('hidden');
                waitingArea.classList.add('hidden');
                
                const remarkText = document.getElementById('detailRemarkText');
                if (feedback.remark) {
                    remarkText.textContent = feedback.remark;
                    remarkText.className = 'text-gray-700 dark:text-gray-200 text-sm leading-relaxed whitespace-pre-wrap font-medium';
                } else {
                    remarkText.textContent = 'Tanggapan ditandai selesai tanpa komentar.';
                    remarkText.className = 'text-gray-400 dark:text-gray-500 text-xs italic font-medium';
                }
            } else {
                responseContainer.classList.remove('hidden');
                completedArea.classList.add('hidden');
                waitingArea.classList.remove('hidden');
            }
            
            // Open drawer
            const drawer = document.getElementById('feedbackDetailDrawer');
            const content = document.getElementById('drawerContent');
            drawer.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('translate-x-full');
            }, 10);
        }

        function closeFeedbackDetail() {
            const drawer = document.getElementById('feedbackDetailDrawer');
            const content = document.getElementById('drawerContent');
            content.classList.add('translate-x-full');
            setTimeout(() => {
                drawer.classList.add('hidden');
            }, 300);
        }

        function toggleSelectAllFeedbacks(source) {
            const checkboxes = document.querySelectorAll('.feedback-checkbox');
            checkboxes.forEach(cb => cb.checked = source.checked);
            updateBulkDeleteBar();
        }

        function updateBulkDeleteBar() {
            const checkboxes = document.querySelectorAll('.feedback-checkbox');
            const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
            const bar = document.getElementById('bulkDeleteBar');
            const countSpan = document.getElementById('selectedCount');
            const selectAll = document.getElementById('selectAllFeedbacks');

            if (checkedCount > 0) {
                bar.classList.remove('hidden');
                bar.classList.add('flex');
                countSpan.textContent = checkedCount;
            } else {
                bar.classList.add('hidden');
                bar.classList.remove('flex');
            }

            if (selectAll) {
                selectAll.checked = (checkedCount === checkboxes.length && checkboxes.length > 0);
            }
        }

        // Close components on ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeCompleteModal();
                closeFeedbackDetail();
                closeFilterModal();
            }
        });

        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const container = document.getElementById('exportDropdownContainer');
            if (container && !container.contains(e.target)) {
                document.getElementById('exportDropdown').classList.add('hidden');
            }
        });
    </script>
</x-app-layout>
