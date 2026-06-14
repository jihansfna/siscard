<x-app-layout>
    <x-slot name="title">Profil Pengguna</x-slot>

    @if($accessStatus !== 'registered')
        <!-- Overlay / Modal for no access or pending confirmation -->
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-md p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden text-center p-8 relative">
                @if($accessStatus === 'inactive')
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
                        <x-heroicon-o-x-circle class="h-8 w-8 text-red-600" />
                    </div>
                    <h2 class="text-2xl font-extrabold text-gray-800 mb-2">Masa Kerja Berakhir</h2>
                    <p class="text-gray-600 mb-8">Maaf, keanggotaan aktif Anda telah dinonaktifkan karena masa kerja Anda di perusahaan telah berakhir (Tanggal Keluar: {{ $employee?->tanggal_keluar?->format('d M Y') ?? '-' }}). Silakan hubungi HR.</p>
                    
                    <form method="POST" action="{{ route('logout') }}" class="form-with-loading">
                        @csrf
                        <button type="submit" class="w-full inline-flex justify-center items-center gap-2 rounded-xl border border-transparent bg-red-600 px-6 py-3 text-base font-bold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all">
                            <span class="btn-text">Keluar</span>
                            <svg class="btn-spinner animate-spin h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </form>
                @elseif($accessStatus === 'no_access')
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
                        <x-heroicon-o-lock-closed class="h-8 w-8 text-red-600" />
                    </div>
                    <h2 class="text-2xl font-extrabold text-gray-800 mb-2">Akses Ditolak</h2>
                    <p class="text-gray-600 mb-8">Maaf, Anda belum terdaftar sebagai Anggota atau tidak memiliki akses ke halaman ini.</p>
                    
                    <form method="POST" action="{{ route('logout') }}" class="form-with-loading">
                        @csrf
                        <button type="submit" class="w-full inline-flex justify-center items-center gap-2 rounded-xl border border-transparent bg-red-600 px-6 py-3 text-base font-bold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all">
                            <span class="btn-text">Keluar</span>
                            <svg class="btn-spinner animate-spin h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </form>
                @elseif($accessStatus === 'pending')
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-6">
                        <x-heroicon-o-exclamation-triangle class="h-8 w-8 text-yellow-600" />
                    </div>
                    <h2 class="text-2xl font-extrabold text-gray-800 mb-2">Konfirmasi Keanggotaan</h2>
                    <p class="text-gray-600 mb-8">Anda telah ditambahkan sebagai anggota oleh admin. Harap konfirmasi untuk mendapatkan akses penuh ke halaman ini.</p>
                    
                    <form method="POST" action="{{ route('user.confirm_membership', $memberId) }}" class="form-with-loading">
                        @csrf
                        <button type="submit" class="w-full inline-flex justify-center items-center gap-2 rounded-xl border border-transparent bg-primary-600 px-6 py-3 text-base font-bold text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all">
                            <span class="btn-text">Ya, Saya Konfirmasi</span>
                            <svg class="btn-spinner animate-spin h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @endif

    {{-- ═══ PROFILE + DIGITAL CARD ═══ --}}
    <div class="grid gap-6 md:grid-cols-3 mb-6 {{ $accessStatus !== 'registered' ? 'blur-md pointer-events-none select-none opacity-50' : '' }}">
        {{-- Profile Card --}}
        <div class="md:col-span-1 min-w-0">
            <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-2xl p-6 shadow-sm flex flex-col items-center text-center h-full transition-colors">
                <div class="w-24 h-24 rounded-2xl overflow-hidden shadow-lg mb-4 bg-gradient-to-br from-primary-800 to-primary-500 flex items-center justify-center flex-shrink-0 ring-4 ring-primary-100 dark:ring-primary-900/30 relative">
                    @if($employee && $employee->foto)
                        <!-- Loading Spinner -->
                        <div id="profilePhotoLoading" class="absolute inset-0 bg-gray-50 dark:bg-gray-800 flex items-center justify-center z-10">
                            <svg class="animate-spin w-6 h-6 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                        <img src="{{ asset('storage/' . $employee->foto) }}" alt="Profile" class="w-full h-full object-cover opacity-0 transition-opacity duration-300 relative z-20" onload="document.getElementById('profilePhotoLoading')?.remove(); this.classList.remove('opacity-0');">
                    @else
                        <span class="text-white text-3xl font-extrabold relative z-20">{{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}</span>
                    @endif
                </div>
                <h2 class="text-lg font-bold text-gray-800 dark:text-white">{{ Auth::user()->nama }}</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">{{ $employee->jabatan ?? 'Karyawan' }}</p>

                <div class="mt-4 w-full space-y-2.5 text-left">
                    <div class="flex items-center justify-between py-2 border-b border-gray-50 dark:border-gray-800">
                        <span class="text-xs text-gray-400 font-medium">Bergabung</span>
                        <span class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $member?->disetujui_pada?->format('d M Y') ?? Auth::user()->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-xs text-gray-400 font-medium">Status</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Aktif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Digital Card Preview --}}
        <div class="md:col-span-2 min-w-0 flex flex-col h-full">
            <div class="rounded-2xl overflow-hidden shadow-lg relative flex flex-col h-full" style="background: linear-gradient(135deg, #1b007c 0%, #3730a3 100%);">
                <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 20% 50%, white 1px, transparent 1px), radial-gradient(circle at 80% 20%, white 1px, transparent 1px); background-size: 40px 40px;"></div>
                
                <div class="relative p-6 sm:p-8 flex flex-col sm:flex-row items-center sm:items-start justify-between gap-6 flex-1">
                    <div class="text-center sm:text-left flex-1 max-w-md">
                        <p class="text-indigo-300 text-xs font-bold uppercase tracking-widest mb-4 flex items-center justify-center sm:justify-start gap-2">
                            <x-heroicon-s-identification class="w-4 h-4" />
                            Kartu Anggota Digital
                        </p>
                        <p class="text-white text-base sm:text-lg leading-relaxed font-medium">
                            Silahkan download kartu digital SPSI anda berikut dibawah ini atau anda bisa scan barcode yang tersedia.
                        </p>
                    </div>
                    
                    <div class="flex-shrink-0 w-36 h-36 bg-white rounded-2xl flex items-center justify-center shadow-2xl overflow-hidden p-3 ring-4 ring-white/20 relative">
                        @if($member && $member->uuid)
                            <!-- Loading Spinner -->
                            <div id="qrCodeLoading" class="absolute inset-0 bg-gray-50 flex items-center justify-center z-10">
                                <svg class="animate-spin w-8 h-8 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </div>
                            <img src="{{ route('qr.image', ['data' => url('/verify/' . $member->verify_token)]) }}" alt="QR Code Verifikasi" class="w-full h-full object-contain opacity-0 transition-opacity duration-300 relative z-20" onload="document.getElementById('qrCodeLoading')?.remove(); this.classList.remove('opacity-0');">
                        @else
                            <div class="w-full h-full bg-gray-50 flex items-center justify-center text-gray-400 text-xs font-medium">QR Code</div>
                        @endif
                    </div>
                </div>

                <div class="px-6 sm:px-8 pb-6 relative mt-auto">
                    <div class="h-px bg-white/10 mb-5"></div>
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <a href="{{ route('user.card.download') }}"
                           id="btnDownloadCard"
                           class="inline-flex w-full sm:w-auto justify-center items-center gap-2 px-6 py-3 bg-white text-primary-800 rounded-xl text-sm font-bold hover:bg-indigo-50 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 transition-all shadow focus:outline-none">
                            <x-heroicon-o-arrow-down-tray class="w-5 h-5 download-icon" />
                            <svg class="download-spinner animate-spin w-5 h-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span class="download-text">Unduh Kartu Digital (PDF)</span>
                        </a>
                        <p class="text-indigo-300/80 text-xs text-center sm:text-right hidden sm:block">Format resmi barcode<br>SPSI PT XYZ</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Grid: Feedback Hub -->
    <div class="grid gap-6 md:grid-cols-3 {{ $accessStatus !== 'registered' ? 'blur-md pointer-events-none select-none opacity-50' : '' }}">
        <!-- Left: Kirim Feedback Form -->
        <div class="md:col-span-1 min-w-0">
            <div class="bg-white dark:bg-gray-900 border border-blue-100 dark:border-gray-700 rounded-2xl p-6 shadow-sm h-full flex flex-col justify-between transition-colors">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-3">Kirim Saran / Masukan</h3>
                    
                    <form action="{{ route('user.feedbacks.store') }}" method="POST" enctype="multipart/form-data" class="form-with-loading space-y-4">
                        @csrf
                        <div>
                            <label for="deskripsi" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Pesan Anda <span class="text-red-500">*</span></label>
                            <textarea id="deskripsi" name="deskripsi" rows="4" required placeholder="Tulis saran, masukan, atau keluhan Anda di sini..." class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm resize-none"></textarea>
                        </div>
                        
                        <div class="space-y-2">
                            <label for="dokumen" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Lampiran Dokumen / Berkas <span class="text-xs text-gray-400 font-normal">(Opsional)</span></label>
                            <input type="file" id="dokumen" name="dokumen" class="block w-full text-sm text-gray-500 border border-gray-200 rounded-xl cursor-pointer bg-gray-50 dark:text-gray-400 dark:bg-gray-800 dark:border-gray-700 focus:outline-none file:cursor-pointer file:bg-primary-50 dark:file:bg-primary-900/30 file:border-0 file:border-r file:border-solid file:border-gray-200 dark:file:border-gray-700 file:!mr-4 file:!py-3 file:!px-5 dark:file:text-primary-400 file:!text-primary-700 file:!font-bold hover:file:bg-primary-100 dark:hover:file:bg-primary-900/50 transition-all" />
                            <p class="text-[10px] text-gray-400 font-semibold leading-normal">Mendukung PDF, Word, Excel, Gambar, atau ZIP (Maks. 10MB)</p>
                        </div>
                        
                        <p class="text-xs text-gray-500 dark:text-gray-400">Masukan Anda akan dikirim langsung dan ditinjau oleh HRD.</p>

                        {{-- Toggle Anonim --}}
                        <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                            <label for="anonim" class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-0.5">
                                <input type="checkbox" id="anonim" name="anonim" value="1" class="sr-only peer">
                                <div class="w-9 h-5 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:after:border-gray-500 peer-checked:bg-primary-600"></div>
                            </label>
                            <div>
                                <p class="text-sm font-bold text-gray-700 dark:text-gray-300">Kirim sebagai anonim</p>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">Identitas Anda tidak akan ditampilkan kepada admin saat meninjau saran ini.</p>
                            </div>
                        </div>

                        <button type="submit" class="w-full justify-center px-6 py-3 text-sm font-bold bg-primary-600 hover:bg-primary-700 text-white rounded-xl shadow-lg shadow-primary-600/20 transition-all flex items-center gap-2 cursor-pointer">
                            <span class="btn-text">Kirim Masukan</span>
                            <svg class="btn-spinner animate-spin h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right: Riwayat Feedback Saya -->
        <div class="md:col-span-2 min-w-0">
            <div class="bg-white dark:bg-gray-900 border border-blue-100 dark:border-gray-700 rounded-2xl p-6 shadow-sm h-full flex flex-col transition-colors">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-3">Riwayat Saran Saya</h3>
                
                <div class="flex-1">
                    <!-- Desktop Table View -->
                    <div class="hidden md:block border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden bg-white dark:bg-gray-900">
                        <table class="w-full text-left text-sm text-gray-600 dark:text-gray-400">
                            <thead class="bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-[10px] uppercase font-semibold border-b border-gray-200 dark:border-gray-700">
                                <tr>
                                    <th class="px-4 py-3 w-10">No</th>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Konten</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 w-16 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse($feedbacks as $index => $fb)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-[#2A2A2A]/50 transition-colors">
                                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $feedbacks->firstItem() + $index }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $fb->created_at->format('d M Y, H:i') }}</td>
                                        <td class="px-4 py-3 max-w-[220px] truncate text-gray-800 dark:text-gray-200" title="{{ $fb->deskripsi }}">
                                            {{ $fb->deskripsi }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if($fb->status === 'Completed')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">Selesai</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400">Menunggu</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center gap-1.5">
                                                @php
                                                    $fbDetail = [
                                                        'id' => $fb->id,
                                                        'date' => $fb->created_at->format('d F Y, H:i'),
                                                        'description' => $fb->deskripsi,
                                                        'file' => $fb->berkas,
                                                        'status' => $fb->status,
                                                        'remark' => $fb->catatan ?? '',
                                                    ];
                                                @endphp
                                                <button type="button" 
                                                    data-feedback-detail="{{ json_encode($fbDetail) }}" 
                                                    onclick="openFeedbackDetailModal(this)"
                                                    class="inline-flex p-1.5 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors cursor-pointer" 
                                                    title="Lihat Detail">
                                                    <x-heroicon-o-eye class="w-5 h-5" />
                                                </button>
                                                <form action="{{ route('user.feedbacks.destroy', $fb->id) }}" method="POST" class="inline-flex">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex p-1.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors cursor-pointer" title="Hapus Saran">
                                                        <x-heroicon-o-trash class="w-5 h-5" />
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-16 text-center">
                                            <div class="flex flex-col items-center justify-center gap-3">
                                                <div class="w-16 h-16 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                                                    <x-heroicon-o-chat-bubble-bottom-center-text class="w-8 h-8 text-indigo-400" />
                                                </div>
                                                <p class="font-bold text-gray-700 dark:text-gray-300">Belum ada saran dikirim</p>
                                                <p class="text-sm text-gray-400 dark:text-gray-500 max-w-xs">Gunakan form di sebelah kiri untuk menyampaikan saran atau masukan Anda kepada HRD.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Accordion View (Beautiful & Natural) -->
                    <div class="block md:hidden space-y-3">
                        @forelse($feedbacks as $index => $fb)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden bg-white dark:bg-gray-900 shadow-sm transition-all" id="accordion-fb-{{ $fb->id }}">
                                <!-- Accordion Header -->
                                <button type="button" 
                                    onclick="toggleFeedbackAccordion({{ $fb->id }})"
                                    class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-50/50 dark:hover:bg-[#2A2A2A]/50 transition-colors focus:outline-none select-none cursor-pointer">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <span class="text-xs font-bold text-gray-400 dark:text-gray-500">#{{ $feedbacks->firstItem() + $index }}</span>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-gray-800 dark:text-gray-200">{{ $fb->created_at->format('d M Y, H:i') }}</p>
                                            <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate max-w-[180px] mt-0.5">{{ $fb->deskripsi }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        @if($fb->status === 'Completed')
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">Selesai</span>
                                        @else
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400">Menunggu</span>
                                        @endif
                                        <x-heroicon-o-chevron-down class="w-4 h-4 text-gray-400 transition-transform duration-200" id="chevron-fb-{{ $fb->id }}" />
                                    </div>
                                </button>

                                <!-- Accordion Content (Slides Down/Up) -->
                                <div id="content-fb-{{ $fb->id }}" class="hidden border-t border-gray-100 dark:border-gray-700 bg-gray-50/40 dark:bg-gray-800/40 p-4 space-y-4">
                                    <div>
                                        <p class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Konten Saran</p>
                                        <p class="text-gray-700 dark:text-gray-300 text-xs leading-relaxed whitespace-pre-wrap font-normal">{{ $fb->deskripsi }}</p>
                                    </div>

                                    @if($fb->berkas)
                                        <div>
                                            <p class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1.5">Berkas / Lampiran</p>
                                            <div class="flex items-center justify-between p-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
                                                <div class="flex items-center gap-2 min-w-0">
                                                    <x-heroicon-o-document-text class="w-5 h-5 text-primary-600 dark:text-primary-400 flex-shrink-0" />
                                                    <div class="min-w-0">
                                                        <p class="text-xs font-bold text-gray-800 dark:text-gray-200 truncate">{{ basename($fb->berkas) }}</p>
                                                    </div>
                                                </div>
                                                <a href="/storage/{{ $fb->berkas }}" download class="flex-shrink-0 flex items-center gap-1 px-2.5 py-1 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-[10px] font-bold transition-all cursor-pointer">
                                                    <span>Unduh</span>
                                                    <x-heroicon-o-arrow-down-tray class="w-3 h-3" />
                                                </a>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                                        <p class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Tanggapan HRD</p>
                                        @if($fb->status === 'Completed')
                                            <div class="p-3.5 bg-primary-50/40 dark:bg-primary-900/20 border border-primary-100/80 dark:border-primary-800/30 rounded-2xl shadow-sm">
                                                <div class="flex items-start gap-2.5">
                                                    <div class="p-1 bg-primary-100 dark:bg-primary-900/40 rounded-lg text-primary-700 dark:text-primary-400 flex-shrink-0 mt-0.5">
                                                        <x-heroicon-s-chat-bubble-left-ellipsis class="w-3.5 h-3.5" />
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-gray-700 dark:text-gray-300 text-xs leading-relaxed whitespace-pre-wrap font-medium">
                                                            {{ $fb->catatan ?? 'Saran telah ditandai selesai tanpa komentar.' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="p-3.5 bg-amber-50/30 dark:bg-amber-900/10 border border-amber-100/80 dark:border-amber-800/30 rounded-2xl shadow-sm flex items-start gap-2.5">
                                                <div class="p-1 bg-amber-100 dark:bg-amber-900/40 rounded-lg text-amber-700 dark:text-amber-400 flex-shrink-0 mt-0.5">
                                                    <x-heroicon-o-clock class="w-3.5 h-3.5" />
                                                </div>
                                                <p class="text-amber-800 dark:text-amber-400 text-[10px] font-semibold leading-relaxed">Menunggu tanggapan HRD...</p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="pt-3 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                                        <form action="{{ route('user.feedbacks.destroy', $fb->id) }}" method="POST" class="inline-flex">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors cursor-pointer">
                                                <x-heroicon-o-trash class="w-4 h-4" />
                                                <span>Hapus Saran</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-16 text-center flex flex-col items-center justify-center gap-3">
                                <div class="w-16 h-16 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                                    <x-heroicon-o-chat-bubble-bottom-center-text class="w-8 h-8 text-indigo-400" />
                                </div>
                                <p class="font-bold text-gray-700 dark:text-gray-300">Belum ada saran dikirim</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500 max-w-xs">Gunakan form di sebelah kiri untuk menyampaikan saran kepada HRD.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                
                @if(method_exists($feedbacks, 'total'))
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Showing <span class="font-bold text-gray-900 dark:text-white">{{ $feedbacks->firstItem() ?? 0 }}</span> to <span class="font-bold text-gray-900 dark:text-white">{{ $feedbacks->lastItem() ?? 0 }}</span> of <span class="font-bold text-gray-900 dark:text-white">{{ $feedbacks->total() }}</span> results
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-3">
                        <div class="inline-flex -space-x-px text-sm rounded-lg shadow-sm">
                            {{-- Previous Page Link --}}
                            @if ($feedbacks->onFirstPage())
                                <span class="flex items-center justify-center px-3 py-2 ms-0 leading-tight text-gray-400 bg-white border border-gray-200 rounded-s-lg dark:bg-gray-800 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed">Previous</span>
                            @else
                                <a href="{{ $feedbacks->previousPageUrl() }}" class="flex items-center justify-center px-3 py-2 ms-0 leading-tight text-gray-500 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-50 hover:text-gray-700 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-[#2A2A2A] dark:hover:text-white transition-colors">Previous</a>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($feedbacks->links()->elements as $element)
                                @if (is_string($element))
                                    <span class="flex items-center justify-center px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-200 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400">{{ $element }}</span>
                                @endif

                                @if (is_array($element))
                                    @foreach ($element as $page => $url)
                                        @if ($page == $feedbacks->currentPage())
                                            <span class="flex items-center justify-center px-3.5 py-2 leading-tight text-white bg-indigo-500 border border-indigo-500 dark:border-indigo-600 dark:bg-indigo-600 font-medium z-10">{{ $page }}</span>
                                        @else
                                            <a href="{{ $url }}" class="flex items-center justify-center px-3.5 py-2 leading-tight text-gray-500 bg-white border border-gray-200 hover:bg-gray-50 hover:text-gray-700 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-[#2A2A2A] dark:hover:text-white transition-colors">{{ $page }}</a>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($feedbacks->hasMorePages())
                                <a href="{{ $feedbacks->nextPageUrl() }}" class="flex items-center justify-center px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-50 hover:text-gray-700 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-[#2A2A2A] dark:hover:text-white transition-colors">Next</a>
                            @else
                                <span class="flex items-center justify-center px-3 py-2 leading-tight text-gray-400 bg-white border border-gray-200 rounded-e-lg dark:bg-gray-800 dark:border-gray-700 dark:text-gray-500 cursor-not-allowed">Next</span>
                            @endif
                        </div>

                        <select onchange="window.location.href = '?perPage=' + this.value" class="bg-white border border-gray-200 text-gray-600 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block py-1.5 pl-3 pr-8 dark:bg-gray-900 dark:border-gray-700 dark:placeholder-gray-400 dark:text-gray-300 transition-colors shadow-sm outline-none appearance-none bg-no-repeat cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%239CA3AF%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-position: right 0.7rem top 50%; background-size: 0.65rem auto;">
                            <option value="5" {{ request('perPage', 5) == 5 ? 'selected' : '' }}>5 per page</option>
                            <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10 per page</option>
                            <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25 per page</option>
                        </select>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- User Feedback Detail Off-Canvas Drawer -->
    <div id="userFeedbackDetailDrawer" onclick="if(event.target === this) closeFeedbackDetailModal()" class="fixed inset-0 z-50 hidden bg-gray-900/50 backdrop-blur-sm transition-opacity">
        <div class="fixed inset-y-0 right-0 w-full max-w-md bg-white dark:bg-gray-800 shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col border-l border-transparent dark:border-gray-700" id="drawerContent">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-white dark:bg-gray-800 z-10 top-0">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white leading-none">Detail Saran</h3>
                    <div class="mt-2">
                        <span id="modalStatusBadge" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold"></span>
                    </div>
                </div>
                <button type="button" onclick="closeFeedbackDetailModal()" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-900 rounded-full transition-colors absolute top-4 right-4 cursor-pointer">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>
            
            <!-- Body -->
            <div class="p-6 overflow-y-auto flex-1 bg-gray-50/50 dark:bg-gray-900 space-y-5">
                <div class="space-y-5">
                    <div>
                        <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Tanggal Dikirim</p>
                        <p class="font-bold text-gray-800 dark:text-gray-200 text-sm" id="modalDate">-</p>
                    </div>
                    
                    <div>
                        <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Konten Saran / Masukan</p>
                        <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed whitespace-pre-wrap font-normal" id="modalDescription">-</p>
                    </div>

                    <!-- File / Document Attached -->
                    <div id="modalFileContainer" class="hidden pt-1">
                        <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Dokumen / Berkas</p>
                        <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <x-heroicon-o-document-text class="w-5.5 h-5.5 text-primary-600 dark:text-primary-400 flex-shrink-0" />
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-gray-800 dark:text-gray-200 truncate" id="modalFileName">-</p>
                                    <p class="text-[10px] text-gray-400 font-semibold leading-none mt-0.5">Berkas lampiran Anda</p>
                                </div>
                            </div>
                            <a id="modalDownloadLink" href="" download class="flex-shrink-0 flex items-center gap-1.5 px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-xs font-bold transition-all cursor-pointer">
                                <span>Unduh</span>
                                <x-heroicon-o-arrow-down-tray class="w-3.5 h-3.5" />
                            </a>
                        </div>
                        <!-- Image Preview -->
                        <div id="modalImagePreviewContainer" class="mt-3 hidden">
                            <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 mb-1.5">Pratinjau Gambar:</p>
                            <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden max-h-48 bg-white dark:bg-gray-800 flex items-center justify-center p-2 relative">
                                <div id="modalImageLoading" class="w-full h-24 flex items-center justify-center hidden">
                                    <svg class="animate-spin h-6 w-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </div>
                                <img id="modalImagePreview" src="" alt="Preview" class="max-w-full max-h-44 object-contain rounded hidden">
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200 dark:border-gray-700">

                <!-- Admin Response Remark Section -->
                <div class="space-y-3">
                    <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Tanggapan HRD</p>
                    
                    <div id="modalRemarkContainer">
                        <!-- If already completed -->
                        <div id="modalCompletedResponseArea" class="hidden">
                            <div class="p-4 bg-primary-50/40 dark:bg-primary-900/20 border border-primary-100/80 dark:border-primary-800/30 rounded-2xl shadow-sm">
                                <div class="flex items-start gap-3">
                                    <div class="p-1.5 bg-primary-100 dark:bg-primary-900/40 rounded-xl text-primary-700 dark:text-primary-400 flex-shrink-0">
                                        <x-heroicon-s-chat-bubble-left-ellipsis class="w-4 h-4" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed whitespace-pre-wrap font-medium" id="modalRemarkText">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- If waiting, show placeholder info -->
                        <div id="modalWaitingResponseArea" class="hidden">
                            <div class="p-4 bg-amber-50/30 dark:bg-amber-900/10 border border-amber-100/80 dark:border-amber-800/30 rounded-2xl shadow-sm flex items-start gap-3">
                                <div class="p-1.5 bg-amber-100 dark:bg-amber-900/40 rounded-xl text-amber-700 dark:text-amber-400 flex-shrink-0">
                                    <x-heroicon-o-clock class="w-4 h-4" />
                                </div>
                                <p class="text-amber-800 dark:text-amber-400 text-xs font-semibold leading-relaxed">Menunggu tanggapan HRD...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openFeedbackDetailModal(btn) {
            const feedback = JSON.parse(btn.dataset.feedbackDetail);
            
            // Set date and description
            document.getElementById('modalDate').textContent = feedback.date;
            document.getElementById('modalDescription').textContent = feedback.description;
            
            // Status Badge
            const badge = document.getElementById('modalStatusBadge');
            if (feedback.status === 'Completed') {
                badge.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-800';
                badge.textContent = 'Selesai';
            } else {
                badge.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-yellow-100 text-yellow-800';
                badge.textContent = 'Menunggu';
            }
            
            // File Handling
            const fileContainer = document.getElementById('modalFileContainer');
            const imagePreviewContainer = document.getElementById('modalImagePreviewContainer');
            const previewImg = document.getElementById('modalImagePreview');
            const previewLoading = document.getElementById('modalImageLoading');
            
            previewImg.classList.add('hidden');
            
            if (feedback.file) {
                fileContainer.classList.remove('hidden');
                
                const fileName = feedback.file.split('/').pop();
                document.getElementById('modalFileName').textContent = fileName;
                
                const fileUrl = '/storage/' + feedback.file;
                document.getElementById('modalDownloadLink').href = fileUrl;
                
                const ext = fileName.split('.').pop().toLowerCase();
                if (['jpg', 'jpeg', 'png'].includes(ext)) {
                    previewLoading.classList.remove('hidden');
                    previewImg.onload = function() {
                        previewLoading.classList.add('hidden');
                        previewImg.classList.remove('hidden');
                    };
                    previewImg.src = fileUrl;
                    imagePreviewContainer.classList.remove('hidden');
                } else {
                    imagePreviewContainer.classList.add('hidden');
                }
            } else {
                fileContainer.classList.add('hidden');
                imagePreviewContainer.classList.add('hidden');
            }
            
            // Remark Handling
            const completedArea = document.getElementById('modalCompletedResponseArea');
            const waitingArea = document.getElementById('modalWaitingResponseArea');
            const remarkText = document.getElementById('modalRemarkText');
            
            if (feedback.status === 'Completed') {
                completedArea.classList.remove('hidden');
                waitingArea.classList.add('hidden');
                
                if (feedback.remark) {
                    remarkText.textContent = feedback.remark;
                    remarkText.className = 'text-gray-700 text-sm leading-relaxed whitespace-pre-wrap font-medium';
                } else {
                    remarkText.textContent = 'Saran telah ditandai selesai tanpa komentar.';
                    remarkText.className = 'text-gray-400 text-xs italic font-medium';
                }
            } else {
                completedArea.classList.add('hidden');
                waitingArea.classList.remove('hidden');
            }
            
            // Open Drawer
            const drawer = document.getElementById('userFeedbackDetailDrawer');
            const content = drawer.querySelector('#drawerContent');
            drawer.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('translate-x-full');
            }, 10);
        }
        
        function closeFeedbackDetailModal() {
            const drawer = document.getElementById('userFeedbackDetailDrawer');
            const content = drawer.querySelector('#drawerContent');
            content.classList.add('translate-x-full');
            setTimeout(() => {
                drawer.classList.add('hidden');
            }, 300);
        }

        function toggleFeedbackAccordion(id) {
            const content = document.getElementById(`content-fb-${id}`);
            const chevron = document.getElementById(`chevron-fb-${id}`);
            const card = document.getElementById(`accordion-fb-${id}`);
            
            if (content.classList.contains('hidden')) {
                // Open with visual highlight
                content.classList.remove('hidden');
                chevron.classList.add('rotate-180');
                card.classList.add('ring-2', 'ring-primary-500/10', 'border-primary-300');
            } else {
                // Close
                content.classList.add('hidden');
                chevron.classList.remove('rotate-180');
                card.classList.remove('ring-2', 'ring-primary-500/10', 'border-primary-300');
            }
        }

        // Close on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeFeedbackDetailModal();
            }
        });

        // Client-side file size validation for feedback upload
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('document');
            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    const maxSize = 10 * 1024 * 1024; // 10MB
                    if (this.files.length > 0 && this.files[0].size > maxSize) {
                        const sizeMB = (this.files[0].size / (1024 * 1024)).toFixed(1);
                        showToast('Gagal!', `Ukuran file (${sizeMB}MB) melebihi batas maksimum 10MB. Silakan pilih file yang lebih kecil.`, 'error');
                        this.value = '';
                    }
                });
            }
        });

        // Efek loading saat klik unduh kartu PDF
        document.getElementById('btnDownloadCard')?.addEventListener('click', function(e) {
            const btn = this;
            const icon = btn.querySelector('.download-icon');
            const spinner = btn.querySelector('.download-spinner');
            const text = btn.querySelector('.download-text');
            
            // Tampilkan state loading
            icon.classList.add('hidden');
            spinner.classList.remove('hidden');
            text.textContent = 'Menyiapkan PDF...';
            btn.classList.add('pointer-events-none', 'opacity-80');

            // Kembalikan ke state awal setelah 3.5 detik (asumsi waktu download mulai)
            setTimeout(() => {
                icon.classList.remove('hidden');
                spinner.classList.add('hidden');
                text.textContent = 'Unduh Kartu Digital (PDF)';
                btn.classList.remove('pointer-events-none', 'opacity-80');
            }, 3500);
        });
    </script>

    {{-- ═══ FOOTER ═══ --}}
    <footer class="mt-10 pt-6 border-t border-gray-100 dark:border-gray-800">
        <div class="flex justify-center items-center">
            <p class="text-xs text-gray-400 dark:text-gray-500 text-center">
                © 2027 PT XYZ — All Rights Reserved
            </p>
        </div>
    </footer>
</x-app-layout>
