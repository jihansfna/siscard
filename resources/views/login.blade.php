<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login | SISCARD</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased min-h-screen">
    <div class="flex flex-col md:flex-row min-h-screen">
        <!-- Hero Section -->
        <aside class="relative flex-1 bg-gradient-to-br from-primary-800 to-primary-900 overflow-hidden md:rounded-r-[40px] flex flex-col justify-center shadow-2xl z-10 p-10 lg:p-16">
            <div class="w-full max-w-lg mx-auto text-white">
                <div class="mb-8 animate-fade-in-up">
                    <h1 class="text-3xl md:text-4xl font-extrabold leading-tight mb-4 text-transparent bg-clip-text bg-gradient-to-r from-white to-blue-100">Welcome to SISCARD</h1>
                    <p class="text-blue-100 text-lg leading-relaxed">
                        <strong class="text-white font-bold">Log in</strong> to stay united and empowered.
                        <br>
                        Built to support every member of SPSI.
                    </p>
                </div>

                <div class="flex justify-center mt-8 animate-fade-in-up" style="animation-delay: 200ms;">
                    <img src="{{ asset('login_vector.png') }}" alt="Siscard illustration" class="w-64 md:w-80 lg:w-96 object-contain drop-shadow-2xl hover:scale-105 transition-transform duration-500" fetchpriority="high">
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
                    <h2 class="text-3xl font-extrabold text-primary-800 tracking-tight">Login!</h2>
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

                <form id="login-form" method="POST" action="{{ route('login.submit') }}" class="space-y-6">
                    @csrf

                    <div class="space-y-2 group">
                        <label for="badge" class="block text-sm font-bold text-gray-700 group-focus-within:text-primary-700 transition-colors">Badge ID</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary-500 transition-colors">
                                <x-heroicon-o-identification class="w-5 h-5" />
                            </div>
                            <input id="badge" name="badge" type="text" value="{{ old('badge') }}" placeholder="Enter your badge ID" autocomplete="username" required 
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                        </div>
                    </div>

                    <div class="space-y-2 group">
                        <label for="password" class="block text-sm font-bold text-gray-700 group-focus-within:text-primary-700 transition-colors">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary-500 transition-colors">
                                <x-heroicon-o-lock-closed class="w-5 h-5" />
                            </div>
                            <input id="password" name="password" type="password" placeholder="Enter your password" autocomplete="current-password" required 
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
                        
                        <button type="button" id="show-reset-btn" class="text-sm font-bold text-primary-600 hover:text-primary-800 transition-colors">Reset Password</button>
                    </div>

                    <div class="pt-4 flex justify-center">
                        <button type="submit" id="login-btn" class="w-full max-w-[200px] bg-primary-600 hover:bg-primary-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-primary-600/30 hover:shadow-xl hover:shadow-primary-600/40 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all duration-300 ease-out flex justify-center items-center gap-2">
                            <span id="btn-text">Login</span>
                            <x-heroicon-m-arrow-right id="btn-icon" class="w-5 h-5" />
                            <svg id="btn-spinner" class="animate-spin h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Reset Password Form -->
                <form id="reset-form" method="POST" action="{{ route('password.reset.submit') }}" class="space-y-6 hidden">
                    @csrf

                    <div class="space-y-2 group">
                        <label for="reset_badge" class="block text-sm font-bold text-gray-700 group-focus-within:text-primary-700 transition-colors">Badge ID</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary-500 transition-colors">
                                <x-heroicon-o-identification class="w-5 h-5" />
                            </div>
                            <input id="reset_badge" name="badge" type="text" placeholder="Enter your badge ID to reset" required 
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Password akan direset ke password default.</p>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <button type="button" id="show-login-btn" class="text-sm font-bold text-gray-600 hover:text-gray-900 transition-colors flex items-center gap-1">
                            <x-heroicon-m-arrow-left class="w-4 h-4" />
                            Kembali ke Login
                        </button>
                    </div>

                    <div class="pt-4 flex justify-center">
                        <button type="submit" id="reset-btn" class="w-full max-w-[200px] bg-red-600 hover:bg-red-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-red-600/30 hover:shadow-xl hover:shadow-red-600/40 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all duration-300 ease-out flex justify-center items-center gap-2">
                            <span id="reset-btn-text">Reset</span>
                            <x-heroicon-m-arrow-path id="reset-btn-icon" class="w-5 h-5" />
                            <svg id="reset-btn-spinner" class="animate-spin h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </section>
        </main>
    </div>
    
    <script>
        // Form Toggle Logic
        const loginForm = document.getElementById('login-form');
        const resetForm = document.getElementById('reset-form');
        const formTitle = document.querySelector('h2.text-3xl');
        
        document.getElementById('show-reset-btn').addEventListener('click', function() {
            loginForm.classList.add('hidden');
            resetForm.classList.remove('hidden');
            formTitle.innerText = 'Reset Password';
        });

        document.getElementById('show-login-btn').addEventListener('click', function() {
            resetForm.classList.add('hidden');
            loginForm.classList.remove('hidden');
            formTitle.innerText = 'Login!';
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
            text.innerText = 'Loading...';
            icon.classList.add('hidden');
            spinner.classList.remove('hidden');
        });

        // Form Submit Loading State - Reset
        document.getElementById('reset-form').addEventListener('submit', function() {
            const btn = document.getElementById('reset-btn');
            const text = document.getElementById('reset-btn-text');
            const icon = document.getElementById('reset-btn-icon');
            const spinner = document.getElementById('reset-btn-spinner');
            
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            text.innerText = 'Loading...';
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
