<x-app-layout>
    <x-slot name="title">User Profile</x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-semibold flex items-center gap-3">
            <x-heroicon-s-check-circle class="w-5 h-5 flex-shrink-0" />
            {{ session('success') }}
        </div>
    @endif

    @if($accessStatus !== 'registered')
        <!-- Overlay / Modal for no access or pending confirmation -->
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-md p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden text-center p-8 relative">
                @if($accessStatus === 'no_access')
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

    <div class="grid gap-6 md:grid-cols-3 {{ $accessStatus !== 'registered' ? 'blur-md pointer-events-none select-none opacity-50' : '' }}">
        <!-- Profile Sidebar -->
        <div class="md:col-span-1">
            <div class="bg-white border border-blue-100 rounded-2xl p-6 shadow-sm flex flex-col items-center text-center">
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
            <div class="bg-white border border-blue-100 rounded-2xl p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3">Informasi Akun</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6">
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
            
            <!-- Feedback Section -->
            <div class="bg-white border border-blue-100 rounded-2xl p-6 shadow-sm mt-6">
                <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
                    <h3 class="text-lg font-bold text-gray-800">Feedback Saya</h3>
                    @if($accessStatus === 'registered')
                        <button type="button" onclick="document.getElementById('addFeedbackModal').classList.remove('hidden'); document.getElementById('addFeedbackModal').classList.add('flex')" class="flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm shadow-primary-600/30">
                            <span>Tambah saran/masukan</span>
                            <x-heroicon-o-plus class="w-4 h-4" />
                        </button>
                    @endif
                </div>

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
                    <div class="py-8 text-center text-gray-500 flex flex-col items-center">
                        <x-heroicon-o-chat-bubble-bottom-center-text class="w-10 h-10 text-gray-300 mb-2" />
                        <p>Anda belum pernah mengirimkan feedback.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add Feedback Modal -->
    <div id="addFeedbackModal" class="fixed inset-0 z-50 {{ $errors->any() && session('error_modal') == 'addFeedback' ? 'flex' : 'hidden' }} items-center justify-center bg-gray-900/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Kirim Saran / Masukan</h3>
                <button type="button" onclick="document.getElementById('addFeedbackModal').classList.add('hidden'); document.getElementById('addFeedbackModal').classList.remove('flex')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
            
            <form action="{{ route('user.feedbacks.store') }}" method="POST" class="form-with-loading">
                @csrf
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label for="description" class="block text-sm font-bold text-gray-700 mb-1">Pesan Anda <span class="text-red-500">*</span></label>
                            <textarea id="description" name="description" rows="4" required placeholder="Tuliskan saran, masukan, atau keluhan Anda di sini..." class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm resize-none"></textarea>
                        </div>
                        <p class="text-xs text-gray-500">Feedback Anda akan dikirim ke sistem untuk ditinjau oleh HRD.</p>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('addFeedbackModal').classList.add('hidden'); document.getElementById('addFeedbackModal').classList.remove('flex')" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-200 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 text-sm font-bold bg-primary-600 hover:bg-primary-700 text-white rounded-xl shadow-lg shadow-primary-600/20 transition-all flex items-center gap-2">
                        <span class="btn-text">Kirim saran/masukan</span>
                        <svg class="btn-spinner animate-spin h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
