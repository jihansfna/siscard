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
        <div class="md:col-span-1">
            <div class="bg-white border border-blue-100 rounded-2xl p-6 shadow-sm flex flex-col items-center text-center h-full justify-center">
                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-primary-800 to-primary-500 text-white flex items-center justify-center text-3xl font-extrabold shadow-lg mb-4">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
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
        <div class="md:col-span-2">
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
        <div class="md:col-span-1">
            <div class="bg-white border border-blue-100 rounded-2xl p-6 shadow-sm h-full flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3">Kirim Saran / Masukan</h3>
                    
                    <form action="{{ route('user.feedbacks.store') }}" method="POST" class="form-with-loading space-y-4">
                        @csrf
                        <div>
                            <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Pesan Anda <span class="text-red-500">*</span></label>
                            <textarea id="description" name="description" rows="5" required placeholder="Tuliskan saran, masukan, atau keluhan Anda di sini..." class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm resize-none"></textarea>
                        </div>
                        <p class="text-xs text-gray-500">Feedback Anda akan langsung terkirim dan ditinjau oleh HRD.</p>
                        
                        <button type="submit" class="w-full justify-center px-6 py-3 text-sm font-bold bg-primary-600 hover:bg-primary-700 text-white rounded-xl shadow-lg shadow-primary-600/20 transition-all flex items-center gap-2">
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
        <div class="md:col-span-2">
            <div class="bg-white border border-blue-100 rounded-2xl p-6 shadow-sm h-full flex flex-col">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3">Feedback Saya</h3>
                
                <div class="flex-1 overflow-y-auto max-h-[380px] pr-2 space-y-4">
                    @if(isset($feedbacks) && $feedbacks->count() > 0)
                        <div class="space-y-4">
                            @foreach($feedbacks as $fb)
                                <div class="p-4 border {{ $fb->status === 'Completed' ? 'border-green-200 bg-green-50/30' : 'border-gray-200 bg-gray-50' }} rounded-xl">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="text-sm text-gray-500 font-medium">{{ $fb->created_at->format('d M Y, H:i') }}</div>
                                        @if($fb->status === 'Completed')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Completed</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Waiting</span>
                                        @endif
                                    </div>
                                    <p class="text-gray-800 font-medium text-sm mb-3">{{ $fb->description }}</p>
                                    @if($fb->remark)
                                        <div class="mt-2 p-3 bg-white border border-blue-100 rounded-lg text-sm">
                                            <div class="font-bold text-primary-800 mb-1 flex items-center gap-1">
                                                <x-heroicon-s-chat-bubble-left-ellipsis class="w-4 h-4" /> Balasan HRD:
                                            </div>
                                            <p class="text-gray-700">{{ $fb->remark }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-12 text-center text-gray-500 flex flex-col items-center justify-center h-full">
                            <x-heroicon-o-chat-bubble-bottom-center-text class="w-12 h-12 text-gray-300 mb-3" />
                            <p class="font-medium">Anda belum pernah mengirimkan feedback.</p>
                            <p class="text-xs text-gray-400 mt-1">Gunakan form di sebelah kiri untuk mengirim masukan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Scripts moved to app.js using data-download-card event delegation
    </script>
</x-app-layout>
