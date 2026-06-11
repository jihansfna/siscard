<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Masuk | SISCARD</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased min-h-screen">
    <div class="flex flex-col md:flex-row min-h-screen">
        <!-- Hero Section -->
        <aside class="relative flex-1 bg-gradient-to-br from-primary-800 to-primary-900 overflow-hidden md:rounded-r-[40px] flex flex-col justify-center shadow-2xl z-10 p-10 lg:p-16">
            <div class="w-full max-w-lg mx-auto text-white">
                <div class="mb-8 animate-fade-in-up">
                    <h1 class="text-3xl md:text-4xl font-extrabold leading-tight mb-4 text-transparent bg-clip-text bg-gradient-to-r from-white to-blue-100">Selamat datang di SISCARD</h1>
                    <p class="text-blue-100 text-lg leading-relaxed">
                        <strong class="text-white font-bold">Masuk</strong> untuk tetap bersatu dan berdaya.
                        <br>
                        Dibangun untuk mendukung setiap anggota SPSI.
                    </p>
                </div>

                <div class="flex justify-center mt-8 animate-fade-in-up" style="animation-delay: 200ms;">
                    <img src="{{ asset('login_vector.png') }}" alt="Ilustrasi Siscard" class="w-64 md:w-80 lg:w-96 object-contain drop-shadow-2xl hover:scale-105 transition-transform duration-500" fetchpriority="high">
                </div>
            </div>
            
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 -mt-20 -mr-20 w-80 h-80 bg-white opacity-5 rounded-full blur-3xl mix-blend-overlay pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-96 h-96 bg-primary-500 opacity-20 rounded-full blur-3xl mix-blend-overlay pointer-events-none"></div>
        </aside>

        <!-- Form Section -->
        <main class="flex-1 flex items-center justify-center p-6 md:p-12 lg:p-20 relative md:bg-transparent">
            <!-- Mobile background gradient -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 to-white -z-10 block md:hidden"></div>
            
            <section class="w-full max-w-md">
                <div class="mb-10 text-center md:text-left">
                    <h2 id="form-title" class="text-3xl font-extrabold text-primary-800 tracking-tight">Masuk!</h2>
                </div>

                {{-- Global Alert Container --}}
                <div id="alert-success" class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-semibold items-center gap-3 animate-fade-in hidden">
                    <x-heroicon-s-check-circle class="w-5 h-5 flex-shrink-0 inline" />
                    <span id="alert-success-text"></span>
                </div>

                <div id="alert-error" class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-semibold items-start gap-3 animate-fade-in hidden">
                    <x-heroicon-s-exclamation-circle class="w-5 h-5 flex-shrink-0 inline mt-0.5" />
                    <span id="alert-error-text"></span>
                </div>

                @if (session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-semibold flex items-center gap-3 animate-fade-in">
                        <x-heroicon-s-check-circle class="w-5 h-5 flex-shrink-0" />
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-semibold flex items-start gap-3 animate-fade-in">
                        <x-heroicon-s-exclamation-circle class="w-5 h-5 flex-shrink-0 mt-0.5" />
                        <div class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- LOGIN FORM --}}
                <form id="login-form" method="POST" action="{{ route('login.submit') }}" class="space-y-6">
                    @csrf
                    <div class="space-y-2 group">
                        <label for="badge" class="block text-sm font-bold text-gray-700 group-focus-within:text-primary-700 transition-colors">ID Badge</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary-500 transition-colors">
                                <x-heroicon-o-identification class="w-5 h-5" />
                            </div>
                            <input id="badge" name="badge" type="text" value="{{ old('badge') }}" placeholder="Masukkan ID badge Anda" autocomplete="username" required 
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                        </div>
                    </div>

                    <div class="space-y-2 group">
                        <label for="password" class="block text-sm font-bold text-gray-700 group-focus-within:text-primary-700 transition-colors">Kata Sandi</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary-500 transition-colors">
                                <x-heroicon-o-lock-closed class="w-5 h-5" />
                            </div>
                            <input id="password" name="password" type="password" placeholder="Masukkan kata sandi Anda" autocomplete="current-password" required 
                                class="w-full pl-10 pr-14 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                            <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 px-4 flex items-center justify-center text-gray-400 hover:text-primary-600 focus:outline-none transition-colors rounded-r-xl">
                                <x-heroicon-o-eye id="eye-icon" class="w-5 h-5" />
                                <x-heroicon-o-eye-slash id="eye-slash-icon" class="w-5 h-5 hidden" />
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 cursor-pointer">
                            <span class="text-sm font-medium text-gray-600 group-hover:text-gray-900 transition-colors">Ingat saya</span>
                        </label>
                        <button type="button" id="show-reset-btn" class="text-sm font-bold text-primary-600 hover:text-primary-800 transition-colors">Lupa Kata Sandi?</button>
                    </div>

                    <div class="pt-4 flex justify-center">
                        <button type="submit" id="login-btn" class="w-full max-w-[200px] bg-primary-600 hover:bg-primary-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-primary-600/30 hover:shadow-xl hover:shadow-primary-600/40 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all duration-300 ease-out flex justify-center items-center gap-2">
                            <span id="btn-text">Masuk</span>
                            <x-heroicon-m-arrow-right id="btn-icon" class="w-5 h-5" />
                            <svg id="btn-spinner" class="animate-spin h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </form>

                {{-- RESET STEP 1: Badge Input --}}
                <div id="reset-step-1" class="space-y-6 hidden">
                    <div class="space-y-2 group">
                        <label for="reset_badge" class="block text-sm font-bold text-gray-700 group-focus-within:text-primary-700 transition-colors">ID Badge</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary-500 transition-colors">
                                <x-heroicon-o-identification class="w-5 h-5" />
                            </div>
                            <input id="reset_badge" type="text" placeholder="Masukkan ID badge Anda" required 
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <button type="button" id="back-to-login-1" class="text-sm font-bold text-gray-600 hover:text-gray-900 transition-colors flex items-center gap-1">
                            <x-heroicon-m-arrow-left class="w-4 h-4" />
                            Kembali ke Masuk
                        </button>
                    </div>

                    <div class="pt-4 flex justify-center">
                        <button type="button" id="verify-badge-btn" class="w-full max-w-[200px] bg-amber-600 hover:bg-amber-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-amber-600/30 hover:shadow-xl transition-all duration-300 ease-out flex justify-center items-center gap-2">
                            <span>Lanjutkan</span>
                            <x-heroicon-m-arrow-right class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                {{-- RESET STEP 2: Security Question --}}
                <div id="reset-step-2" class="space-y-6 hidden">
                    <div class="p-4 rounded-xl bg-primary-50 border border-primary-200 text-primary-800 text-sm font-medium">
                        <p class="font-bold mb-1">Pertanyaan Keamanan:</p>
                        <p id="security-question-text" class="italic"></p>
                    </div>

                    <div class="space-y-2 group">
                        <label for="security_answer" class="block text-sm font-bold text-gray-700 group-focus-within:text-primary-700 transition-colors">Jawaban Anda</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary-500 transition-colors">
                                <x-heroicon-o-key class="w-5 h-5" />
                            </div>
                            <input id="security_answer" type="password" placeholder="Masukkan jawaban keamanan Anda" required maxlength="255"
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <button type="button" id="back-to-step-1" class="text-sm font-bold text-gray-600 hover:text-gray-900 transition-colors flex items-center gap-1">
                            <x-heroicon-m-arrow-left class="w-4 h-4" />
                            Kembali
                        </button>
                    </div>

                    <div class="pt-4 flex justify-center">
                        <button type="button" id="verify-answer-btn" class="w-full max-w-[200px] bg-amber-600 hover:bg-amber-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-amber-600/30 hover:shadow-xl transition-all duration-300 ease-out flex justify-center items-center gap-2">
                            <span>Verifikasi</span>
                            <x-heroicon-m-shield-check class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                {{-- RESET STEP 3: New Password --}}
                <div id="reset-step-3" class="space-y-6 hidden">
                    <div class="p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-semibold flex items-center gap-3">
                        <x-heroicon-s-check-circle class="w-5 h-5 flex-shrink-0" />
                        Verifikasi berhasil! Silakan buat kata sandi baru.
                    </div>

                    <div class="space-y-2 group">
                        <label for="new_password" class="block text-sm font-bold text-gray-700 group-focus-within:text-primary-700 transition-colors">Kata Sandi Baru</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary-500 transition-colors">
                                <x-heroicon-o-lock-closed class="w-5 h-5" />
                            </div>
                            <input id="new_password" type="password" placeholder="Minimal 8 karakter, huruf besar, kecil & angka" required 
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                        </div>
                    </div>

                    <div class="space-y-2 group">
                        <label for="new_password_confirmation" class="block text-sm font-bold text-gray-700 group-focus-within:text-primary-700 transition-colors">Konfirmasi Kata Sandi</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary-500 transition-colors">
                                <x-heroicon-o-lock-closed class="w-5 h-5" />
                            </div>
                            <input id="new_password_confirmation" type="password" placeholder="Ulangi kata sandi baru" required 
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                        </div>
                    </div>

                    <div class="pt-4 flex justify-center">
                        <button type="button" id="reset-password-btn" class="w-full max-w-[200px] bg-green-600 hover:bg-green-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-green-600/30 hover:shadow-xl transition-all duration-300 ease-out flex justify-center items-center gap-2">
                            <span>Simpan Kata Sandi</span>
                            <x-heroicon-m-check class="w-5 h-5" />
                        </button>
                    </div>
                </div>

            </section>
        </main>
    </div>
    
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const formTitle = document.getElementById('form-title');
        const loginForm = document.getElementById('login-form');
        const step1 = document.getElementById('reset-step-1');
        const step2 = document.getElementById('reset-step-2');
        const step3 = document.getElementById('reset-step-3');
        const alertSuccess = document.getElementById('alert-success');
        const alertError = document.getElementById('alert-error');
        
        let resetBadge = '';
        let resetToken = '';

        function hideAllForms() {
            [loginForm, step1, step2, step3].forEach(el => el.classList.add('hidden'));
        }

        function showAlert(type, message) {
            alertSuccess.classList.add('hidden');
            alertError.classList.add('hidden');
            if (type === 'success') {
                document.getElementById('alert-success-text').textContent = message;
                alertSuccess.classList.remove('hidden');
                alertSuccess.classList.add('flex');
            } else {
                document.getElementById('alert-error-text').textContent = message;
                alertError.classList.remove('hidden');
                alertError.classList.add('flex');
            }
        }

        function clearAlerts() {
            alertSuccess.classList.add('hidden');
            alertSuccess.classList.remove('flex');
            alertError.classList.add('hidden');
            alertError.classList.remove('flex');
        }

        function showForm(target, title) {
            hideAllForms();
            clearAlerts();
            target.classList.remove('hidden');
            formTitle.innerText = title;
        }

        // Navigation
        document.getElementById('show-reset-btn').addEventListener('click', () => showForm(step1, 'Lupa Kata Sandi'));
        document.getElementById('back-to-login-1').addEventListener('click', () => showForm(loginForm, 'Masuk!'));
        document.getElementById('back-to-step-1').addEventListener('click', () => showForm(step1, 'Lupa Kata Sandi'));

        // Step 1: Verify Badge
        document.getElementById('verify-badge-btn').addEventListener('click', async function() {
            const badge = document.getElementById('reset_badge').value.trim();
            if (!badge) { showAlert('error', 'Badge ID wajib diisi.'); return; }

            this.disabled = true;
            this.classList.add('opacity-75');
            try {
                const res = await fetch('{{ route("password.verify_badge") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ badge })
                });
                const data = await res.json();
                if (data.success) {
                    resetBadge = data.badge;
                    document.getElementById('security-question-text').textContent = data.pertanyaan_rahasia;
                    showForm(step2, 'Verifikasi Keamanan');
                } else {
                    let errorMsg = data.message || 'Terjadi kesalahan.';
                    if (data.errors) {
                        const firstField = Object.keys(data.errors)[0];
                        errorMsg = data.errors[firstField][0];
                    }
                    showAlert('error', errorMsg);
                }
            } catch (e) {
                showAlert('error', 'Terjadi kesalahan jaringan.');
            }
            this.disabled = false;
            this.classList.remove('opacity-75');
        });

        // Step 2: Verify Answer
        document.getElementById('verify-answer-btn').addEventListener('click', async function() {
            const answer = document.getElementById('security_answer').value.trim();
            if (!answer) { showAlert('error', 'Jawaban keamanan wajib diisi.'); return; }

            this.disabled = true;
            this.classList.add('opacity-75');
            try {
                const res = await fetch('{{ route("password.verify_answer") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ badge: resetBadge, jawaban_rahasia: answer })
                });
                const data = await res.json();
                if (data.success) {
                    resetToken = data.reset_token;
                    showForm(step3, 'Buat Kata Sandi Baru');
                } else {
                    let errorMsg = data.message || 'Jawaban keamanan tidak sesuai.';
                    if (data.errors) {
                        const firstField = Object.keys(data.errors)[0];
                        errorMsg = data.errors[firstField][0];
                    }
                    showAlert('error', errorMsg);
                }
            } catch (e) {
                showAlert('error', 'Terjadi kesalahan jaringan.');
            }
            this.disabled = false;
            this.classList.remove('opacity-75');
        });

        // Step 3: Set New Password
        document.getElementById('reset-password-btn').addEventListener('click', async function() {
            const pw = document.getElementById('new_password').value;
            const pwConfirm = document.getElementById('new_password_confirmation').value;
            if (!pw || !pwConfirm) { showAlert('error', 'Semua field wajib diisi.'); return; }
            if (pw !== pwConfirm) { showAlert('error', 'Konfirmasi kata sandi tidak cocok.'); return; }
            if (pw.length < 8) { showAlert('error', 'Kata sandi minimal 8 karakter.'); return; }

            this.disabled = true;
            this.classList.add('opacity-75');
            try {
                const res = await fetch('{{ route("password.reset.submit") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ badge: resetBadge, reset_token: resetToken, new_password: pw, new_password_confirmation: pwConfirm })
                });
                const data = await res.json();
                if (data.success) {
                    showForm(loginForm, 'Masuk!');
                    showAlert('success', data.message);
                    resetBadge = '';
                    resetToken = '';
                } else {
                    let errorMsg = data.message || 'Terjadi kesalahan.';
                    if (data.errors) {
                        const firstField = Object.keys(data.errors)[0];
                        // Translate common English password errors to Indonesian manually if needed
                        let firstError = data.errors[firstField][0];
                        if (firstError.includes('uppercase and one lowercase')) {
                            firstError = 'Kata sandi harus mengandung kombinasi huruf besar dan huruf kecil.';
                        } else if (firstError.includes('at least one symbol')) {
                            firstError = 'Kata sandi harus mengandung setidaknya satu simbol khusus.';
                        } else if (firstError.includes('at least one number')) {
                            firstError = 'Kata sandi harus mengandung setidaknya satu angka.';
                        } else if (firstError.includes('at least one letter')) {
                            firstError = 'Kata sandi harus mengandung setidaknya satu huruf.';
                        }
                        errorMsg = firstError;
                    }
                    showAlert('error', errorMsg);
                }
            } catch (e) {
                showAlert('error', 'Terjadi kesalahan jaringan.');
            }
            this.disabled = false;
            this.classList.remove('opacity-75');
        });

        // Toggle Password Visibility
        document.getElementById('toggle-password').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeSlashIcon = document.getElementById('eye-slash-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        });

        // Form Submit Loading State - Login
        document.getElementById('login-form').addEventListener('submit', function() {
            const btn = document.getElementById('login-btn');
            const text = document.getElementById('btn-text');
            const icon = document.getElementById('btn-icon');
            const spinner = document.getElementById('btn-spinner');
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            text.innerText = 'Memuat...';
            icon.classList.add('hidden');
            spinner.classList.remove('hidden');
        });
    </script>

    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.8s ease-out forwards; }
        .animate-fade-in { animation: fadeInUp 0.4s ease-out forwards; }
    </style>
</body>
</html>
