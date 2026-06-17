<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Daftar | SISCARD</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased min-h-screen">
    <div class="flex flex-col md:flex-row min-h-screen">
        <!-- Hero Section -->
        <aside class="relative flex-1 bg-gradient-to-br from-primary-800 to-primary-900 overflow-hidden md:rounded-r-[40px] flex flex-col justify-center shadow-2xl z-10 p-10 lg:p-16">
            <div class="w-full max-w-lg mx-auto text-white">
                <div class="mb-8 animate-fade-in-up">
                    <h1 class="text-3xl md:text-4xl font-extrabold leading-tight mb-4 text-transparent bg-clip-text bg-gradient-to-r from-white to-blue-100">Bergabung dengan SISCARD</h1>
                    <p class="text-blue-100 text-lg leading-relaxed">
                        <strong class="text-white font-bold">Daftar</strong> untuk menjadi bagian dari komunitas.
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
        <main class="flex-1 flex items-center justify-center p-6 md:p-12 lg:p-20 relative bg-white md:bg-transparent overflow-y-auto">
            <!-- Mobile background gradient -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 to-white -z-10 block md:hidden"></div>
            
            <section class="w-full max-w-md bg-white rounded-3xl p-8 shadow-xl shadow-primary-900/5 md:shadow-none md:p-0 my-auto">
                <div class="mb-10 text-center md:text-left">
                    <h2 class="text-3xl font-extrabold text-primary-800 tracking-tight">Daftar</h2>
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

                <form method="POST" action="{{ route('register.submit') }}" class="space-y-5">
                    @csrf

                    <div class="space-y-2 group">
                        <label for="nama" class="block text-sm font-bold text-gray-700 group-focus-within:text-primary-700 transition-colors">Nama Lengkap</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary-500 transition-colors">
                                <x-heroicon-o-user class="w-5 h-5" />
                            </div>
                            <input id="nama" name="nama" type="text" value="{{ old('nama') }}" placeholder="Masukkan nama lengkap" autocomplete="name" required 
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                        </div>
                    </div>

                    <div class="space-y-2 group">
                        <label for="badge" class="block text-sm font-bold text-gray-700 group-focus-within:text-primary-700 transition-colors">ID Badge</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary-500 transition-colors">
                                <x-heroicon-o-identification class="w-5 h-5" />
                            </div>
                            <input id="badge" name="badge" type="text" value="{{ old('badge') }}" placeholder="Masukkan ID badge" autocomplete="username" required 
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                        </div>
                    </div>

                    <div class="space-y-2 group">
                        <label for="password" class="block text-sm font-bold text-gray-700 group-focus-within:text-primary-700 transition-colors">Kata Sandi</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary-500 transition-colors">
                                <x-heroicon-o-lock-closed class="w-5 h-5" />
                            </div>
                            <input id="password" name="password" type="password" placeholder="Min. 8 karakter" autocomplete="new-password" required 
                                class="w-full pl-10 pr-14 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                            <button type="button" onclick="togglePasswordVisibility('password', this)" class="absolute inset-y-0 right-0 px-4 flex items-center justify-center text-gray-400 hover:text-primary-600 focus:outline-none transition-colors rounded-r-xl">
                                <x-heroicon-o-eye class="w-5 h-5 eye-icon" />
                                <x-heroicon-o-eye-slash class="w-5 h-5 eye-slash-icon hidden" />
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2 group">
                        <label for="password_confirmation" class="block text-sm font-bold text-gray-700 group-focus-within:text-primary-700 transition-colors">Konfirmasi Kata Sandi</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary-500 transition-colors">
                                <x-heroicon-o-check-badge class="w-5 h-5" />
                            </div>
                            <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Ulangi kata sandi" autocomplete="new-password" required 
                                class="w-full pl-10 pr-14 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                            <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)" class="absolute inset-y-0 right-0 px-4 flex items-center justify-center text-gray-400 hover:text-primary-600 focus:outline-none transition-colors rounded-r-xl">
                                <x-heroicon-o-eye class="w-5 h-5 eye-icon" />
                                <x-heroicon-o-eye-slash class="w-5 h-5 eye-slash-icon hidden" />
                            </button>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-primary-600/30 hover:shadow-xl hover:shadow-primary-600/40 hover:-translate-y-0.5 transition-all duration-200 flex justify-center items-center gap-2">
                            <span>Daftar Sekarang</span>
                            <x-heroicon-m-arrow-right class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="text-center pt-6 border-t border-gray-100 mt-6">
                        <p class="text-sm font-medium text-gray-500">
                            Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-primary-600 hover:text-primary-800 hover:underline transition-colors">Masuk di sini</a>
                        </p>
                    </div>
                </form>
            </section>
        </main>
    </div>
    
    <script>
        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const eyeIcon = btn.querySelector('.eye-icon');
            const eyeSlashIcon = btn.querySelector('.eye-slash-icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        }
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
