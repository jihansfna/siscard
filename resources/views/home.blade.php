<x-app-layout>
    <x-slot name="title">User Profile</x-slot>

    @if($accessStatus !== 'registered')
        <!-- Overlay / Modal for no access or pending confirmation -->
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-md p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden text-center p-8 relative">
                @if($accessStatus === 'inactive')
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
                        <x-heroicon-o-x-circle class="h-8 w-8 text-red-600" />
                    </div>
                    <h2 class="text-2xl font-extrabold text-gray-800 mb-2">Masa Kerja Berakhir</h2>
                    <p class="text-gray-600 mb-8">Maaf, masa aktif keanggotaan Anda telah dinonaktifkan karena masa kerja Anda di perusahaan telah selesai (End Date: {{ $employee?->end_date?->format('d M Y') ?? '-' }}). Silakan hubungi HRD.</p>
                    
                    <form method="POST" action="{{ route('logout') }}" class="form-with-loading">
                        @csrf
                        <button type="submit" class="w-full inline-flex justify-center items-center gap-2 rounded-xl border border-transparent bg-red-600 px-6 py-3 text-base font-bold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all">
                            <span class="btn-text">Keluar (Logout)</span>
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
                    <p class="text-gray-600 mb-8">Maaf, Anda belum terdaftar sebagai Member atau belum memiliki akses ke halaman ini.</p>
                    
                    <form method="POST" action="{{ route('logout') }}" class="form-with-loading">
                        @csrf
                        <button type="submit" class="w-full inline-flex justify-center items-center gap-2 rounded-xl border border-transparent bg-red-600 px-6 py-3 text-base font-bold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all">
                            <span class="btn-text">Keluar (Logout)</span>
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
                    <p class="text-gray-600 mb-8">Anda telah ditambahkan sebagai member oleh admin. Mohon konfirmasi untuk dapat mengakses halaman ini sepenuhnya.</p>
                    
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

    <!-- Top Grid: Account Details -->
    <div class="grid gap-6 md:grid-cols-3 mb-6 {{ $accessStatus !== 'registered' ? 'blur-md pointer-events-none select-none opacity-50' : '' }}">
        <!-- Profile Sidebar -->
        <div class="md:col-span-1 min-w-0">
            <div class="bg-white border border-blue-100 rounded-2xl p-6 shadow-sm flex flex-col items-center text-center h-full justify-center">
                <div class="w-24 h-24 rounded-full overflow-hidden shadow-lg mb-4 border-2 border-white bg-gradient-to-br from-primary-800 to-primary-500 flex items-center justify-center flex-shrink-0">
                    @if($employee && $employee->image)
                        <img src="{{ asset('storage/' . $employee->image) }}" alt="Profile" class="w-full h-full object-cover">
                    @else
                        <span class="text-white text-3xl font-extrabold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                    @endif
                </div>
                <h2 class="text-xl font-bold text-gray-800">{{ Auth::user()->name }}</h2>
                <p class="text-gray-500 font-medium mt-1">{{ Auth::user()->badge }}</p>
                <div class="mt-4 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-50 text-green-700 text-xs font-semibold border border-green-200">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    Aktif
                </div>
            </div>
        </div>

        <!-- Profile Details -->
        <div class="md:col-span-2 min-w-0">
            <div class="bg-white border border-blue-100 rounded-2xl p-6 shadow-sm h-full flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3">Informasi Akun</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-y-4 gap-x-6">
                        <div>
                            <p class="text-sm font-semibold text-gray-500">Nama Lengkap</p>
                            <p class="font-medium text-gray-800">{{ Auth::user()->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-500">Badge ID</p>
                            <p class="font-medium text-gray-800">{{ Auth::user()->badge }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-500">Bergabung Sejak</p>
                            <p class="font-medium text-gray-800">{{ Auth::user()->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <img id="qrCodeImage" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ Auth::user()->badge }}" alt="QR Code" class="w-20 h-20 rounded border border-gray-200">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">QR Code Saya</p>
                            <p class="text-xs text-gray-500">Gunakan QR ini untuk identifikasi.</p>
                        </div>
                    </div>
                    <button type="button" 
                        data-download-card 
                        data-qr-value="{{ Auth::user()->badge }}" 
                        data-qr-filename="Member_Card_{{ Auth::user()->badge }}.svg"
                        class="flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm shadow-primary-600/20">
                        <span>Download Card</span>
                        <x-heroicon-o-credit-card class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Grid: Feedback Hub -->
    <div class="grid gap-6 md:grid-cols-3 {{ $accessStatus !== 'registered' ? 'blur-md pointer-events-none select-none opacity-50' : '' }}">
        <!-- Left: Kirim Feedback Form -->
        <div class="md:col-span-1 min-w-0">
            <div class="bg-white border border-blue-100 rounded-2xl p-6 shadow-sm h-full flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3">Kirim Saran / Masukan</h3>
                    
                    <form action="{{ route('user.feedbacks.store') }}" method="POST" enctype="multipart/form-data" class="form-with-loading space-y-4">
                        @csrf
                        <div>
                            <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Pesan Anda <span class="text-red-500">*</span></label>
                            <textarea id="description" name="description" rows="4" required placeholder="Tuliskan saran, masukan, atau keluhan Anda di sini..." class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm resize-none"></textarea>
                        </div>
                        
                        <div class="space-y-2">
                            <label for="document" class="block text-sm font-bold text-gray-700">Lampiran Dokumen / File <span class="text-xs text-gray-400 font-normal">(Opsional)</span></label>
                            <input type="file" id="document" name="document" class="block w-full text-xs text-gray-500
                                file:mr-3 file:py-2.5 file:px-4
                                file:rounded-xl file:border-0
                                file:text-xs file:font-bold
                                file:bg-primary-50 file:text-primary-800
                                hover:file:bg-primary-100
                                border border-gray-200 bg-gray-50 rounded-xl cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all" />
                            <p class="text-[10px] text-gray-400 font-semibold leading-normal">Mendukung PDF, Word, Excel, Gambar, atau ZIP (Max. 5MB)</p>
                        </div>
                        
                        <p class="text-xs text-gray-500">Feedback Anda akan langsung terkirim dan ditinjau oleh HRD.</p>
                        
                        <button type="submit" class="w-full justify-center px-6 py-3 text-sm font-bold bg-primary-600 hover:bg-primary-700 text-white rounded-xl shadow-lg shadow-primary-600/20 transition-all flex items-center gap-2 cursor-pointer">
                            <span class="btn-text">Kirim Saran</span>
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
            <div class="bg-white border border-blue-100 rounded-2xl p-6 shadow-sm h-full flex flex-col">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3">Feedback Saya</h3>
                
                <div class="flex-1 overflow-y-auto max-h-[380px] pr-1">
                    <!-- Desktop Table View -->
                    <div class="hidden md:block border border-gray-200 rounded-xl overflow-hidden bg-white">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-700 text-[10px] uppercase font-semibold border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 w-10">No</th>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Isi Saran</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 w-16 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($feedbacks as $index => $fb)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-4 py-3 text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ $fb->created_at->format('d M Y, H:i') }}</td>
                                        <td class="px-4 py-3 max-w-[220px] truncate" title="{{ $fb->description }}">
                                            {{ $fb->description }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if($fb->status === 'Completed')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-green-100 text-green-800">Completed</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-yellow-100 text-yellow-800">Waiting</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center gap-1.5">
                                                @php
                                                    $fbDetail = [
                                                        'id' => $fb->id,
                                                        'date' => $fb->created_at->format('d F Y, H:i'),
                                                        'description' => $fb->description,
                                                        'file' => $fb->file,
                                                        'status' => $fb->status,
                                                        'remark' => $fb->remark ?? '',
                                                    ];
                                                @endphp
                                                <button type="button" 
                                                    data-feedback-detail="{{ json_encode($fbDetail) }}" 
                                                    onclick="openFeedbackDetailModal(this)"
                                                    class="inline-flex p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors cursor-pointer" 
                                                    title="Lihat Detail">
                                                    <x-heroicon-o-eye class="w-5 h-5" />
                                                </button>
                                                <form action="{{ route('user.feedbacks.destroy', $fb->id) }}" method="POST" class="inline-flex">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors cursor-pointer" title="Hapus Saran">
                                                        <x-heroicon-o-trash class="w-5 h-5" />
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-12 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <x-heroicon-o-chat-bubble-bottom-center-text class="w-10 h-10 text-gray-300 mb-3" />
                                                <p class="font-medium text-sm">Anda belum pernah mengirimkan feedback.</p>
                                                <p class="text-xs text-gray-400 mt-1">Gunakan form di sebelah kiri untuk mengirim masukan.</p>
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
                            <div class="border border-gray-200 rounded-xl overflow-hidden bg-white shadow-sm transition-all" id="accordion-fb-{{ $fb->id }}">
                                <!-- Accordion Header -->
                                <button type="button" 
                                    onclick="toggleFeedbackAccordion({{ $fb->id }})"
                                    class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-50/50 transition-colors focus:outline-none select-none cursor-pointer">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <span class="text-xs font-bold text-gray-400">#{{ $index + 1 }}</span>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-gray-800">{{ $fb->created_at->format('d M Y, H:i') }}</p>
                                            <p class="text-[11px] text-gray-500 truncate max-w-[180px] mt-0.5">{{ $fb->description }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        @if($fb->status === 'Completed')
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-semibold bg-green-100 text-green-800">Completed</span>
                                        @else
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-semibold bg-yellow-100 text-yellow-800">Waiting</span>
                                        @endif
                                        <x-heroicon-o-chevron-down class="w-4 h-4 text-gray-400 transition-transform duration-200" id="chevron-fb-{{ $fb->id }}" />
                                    </div>
                                </button>

                                <!-- Accordion Content (Slides Down/Up) -->
                                <div id="content-fb-{{ $fb->id }}" class="hidden border-t border-gray-100 bg-gray-50/40 p-4 space-y-4">
                                    <div>
                                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1">Isi Masukan / Saran</p>
                                        <p class="text-gray-700 text-xs leading-relaxed whitespace-pre-wrap font-normal">{{ $fb->description }}</p>
                                    </div>

                                    @if($fb->file)
                                        <div>
                                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Berkas / Lampiran</p>
                                            <div class="flex items-center justify-between p-2.5 bg-white border border-gray-200 rounded-xl shadow-sm">
                                                <div class="flex items-center gap-2 min-w-0">
                                                    <x-heroicon-o-document-text class="w-5 h-5 text-primary-600 flex-shrink-0" />
                                                    <div class="min-w-0">
                                                        <p class="text-xs font-bold text-gray-800 truncate">{{ basename($fb->file) }}</p>
                                                    </div>
                                                </div>
                                                <a href="/storage/{{ $fb->file }}" download class="flex-shrink-0 flex items-center gap-1 px-2.5 py-1 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-[10px] font-bold transition-all cursor-pointer">
                                                    <span>Unduh</span>
                                                    <x-heroicon-o-arrow-down-tray class="w-3 h-3" />
                                                </a>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="pt-2 border-t border-gray-100">
                                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-2">Balasan HRD</p>
                                        @if($fb->status === 'Completed')
                                            <div class="p-3.5 bg-primary-50/40 border border-primary-100/80 rounded-2xl shadow-sm">
                                                <div class="flex items-start gap-2.5">
                                                    <div class="p-1 bg-primary-100 rounded-lg text-primary-700 flex-shrink-0 mt-0.5">
                                                        <x-heroicon-s-chat-bubble-left-ellipsis class="w-3.5 h-3.5" />
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-gray-700 text-xs leading-relaxed whitespace-pre-wrap font-medium">
                                                            {{ $fb->remark ?? 'Saran telah ditandai selesai tanpa komentar.' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="p-3.5 bg-amber-50/30 border border-amber-100/80 rounded-2xl shadow-sm flex items-start gap-2.5">
                                                <div class="p-1 bg-amber-100 rounded-lg text-amber-700 flex-shrink-0 mt-0.5">
                                                    <x-heroicon-o-clock class="w-3.5 h-3.5" />
                                                </div>
                                                <p class="text-amber-800 text-[10px] font-semibold leading-relaxed">Menunggu tanggapan dari HRD...</p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="pt-3 border-t border-gray-100 flex justify-end">
                                        <form action="{{ route('user.feedbacks.destroy', $fb->id) }}" method="POST" class="inline-flex">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-50 rounded-lg transition-colors cursor-pointer">
                                                <x-heroicon-o-trash class="w-4 h-4" />
                                                <span>Hapus Saran</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center text-gray-500 flex flex-col items-center justify-center">
                                <x-heroicon-o-chat-bubble-bottom-center-text class="w-10 h-10 text-gray-300 mb-3" />
                                <p class="font-medium text-sm">Anda belum pernah mengirimkan feedback.</p>
                                <p class="text-xs text-gray-400 mt-1">Gunakan form di sebelah kiri untuk mengirim masukan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Feedback Detail Off-Canvas Drawer -->
    <div id="userFeedbackDetailDrawer" onclick="if(event.target === this) closeFeedbackDetailModal()" class="fixed inset-0 z-50 hidden bg-gray-900/50 backdrop-blur-sm transition-opacity">
        <div class="fixed inset-y-0 right-0 w-full max-w-md bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col" id="drawerContent">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-white z-10 top-0">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 leading-none">Detail Feedback</h3>
                    <div class="mt-2">
                        <span id="modalStatusBadge" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold"></span>
                    </div>
                </div>
                <button type="button" onclick="closeFeedbackDetailModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors absolute top-4 right-4 cursor-pointer">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>
            
            <!-- Body -->
            <div class="p-6 overflow-y-auto flex-1 bg-gray-50/50 space-y-5">
                <div class="space-y-5">
                    <div>
                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Tanggal Terkirim</p>
                        <p class="font-bold text-gray-800 text-sm" id="modalDate">-</p>
                    </div>
                    
                    <div>
                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Isi Masukan / Saran</p>
                        <p class="text-gray-600 text-sm leading-relaxed whitespace-pre-wrap font-normal" id="modalDescription">-</p>
                    </div>

                    <!-- File / Document Attached -->
                    <div id="modalFileContainer" class="hidden pt-1">
                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-2">Dokumen / File</p>
                        <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-xl shadow-sm">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <x-heroicon-o-document-text class="w-5.5 h-5.5 text-primary-600 flex-shrink-0" />
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-gray-800 truncate" id="modalFileName">-</p>
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
                            <p class="text-[10px] font-semibold text-gray-400 mb-1.5">Pratinjau Gambar:</p>
                            <div class="border border-gray-200 rounded-xl overflow-hidden max-h-48 bg-white flex items-center justify-center p-2">
                                <img id="modalImagePreview" src="" alt="Pratinjau" class="max-w-full max-h-44 object-contain rounded">
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200">

                <!-- Admin Response Remark Section -->
                <div class="space-y-3">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Balasan HRD</p>
                    
                    <div id="modalRemarkContainer">
                        <!-- If already completed -->
                        <div id="modalCompletedResponseArea" class="hidden">
                            <div class="p-4 bg-primary-50/40 border border-primary-100/80 rounded-2xl shadow-sm">
                                <div class="flex items-start gap-3">
                                    <div class="p-1.5 bg-primary-100 rounded-xl text-primary-700 flex-shrink-0">
                                        <x-heroicon-s-chat-bubble-left-ellipsis class="w-4 h-4" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-wrap font-medium" id="modalRemarkText">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- If waiting, show placeholder info -->
                        <div id="modalWaitingResponseArea" class="hidden">
                            <div class="p-4 bg-amber-50/30 border border-amber-100/80 rounded-2xl shadow-sm flex items-start gap-3">
                                <div class="p-1.5 bg-amber-100 rounded-xl text-amber-700 flex-shrink-0">
                                    <x-heroicon-o-clock class="w-4 h-4" />
                                </div>
                                <p class="text-amber-800 text-xs font-semibold leading-relaxed">Menunggu tanggapan dari HRD...</p>
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
                badge.textContent = 'Completed';
            } else {
                badge.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-yellow-100 text-yellow-800';
                badge.textContent = 'Waiting';
            }
            
            // File Handling
            const fileContainer = document.getElementById('modalFileContainer');
            const imagePreviewContainer = document.getElementById('modalImagePreviewContainer');
            
            if (feedback.file) {
                fileContainer.classList.remove('hidden');
                
                const fileName = feedback.file.split('/').pop();
                document.getElementById('modalFileName').textContent = fileName;
                
                const fileUrl = '/storage/' + feedback.file;
                document.getElementById('modalDownloadLink').href = fileUrl;
                
                const ext = fileName.split('.').pop().toLowerCase();
                if (['jpg', 'jpeg', 'png'].includes(ext)) {
                    document.getElementById('modalImagePreview').src = fileUrl;
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
    </script>
</x-app-layout>
