<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Dashboard' }} | SISCARD</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        <div class="flex min-h-screen overflow-x-hidden bg-gradient-to-br from-indigo-50/50 via-white to-blue-50/30">
            @if(Auth::user() && Auth::user()->role === 'admin')
                <!-- Sidebar -->
                <x-sidebar />
            @endif

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 {{ Auth::user() && Auth::user()->role === 'admin' ? 'md:ml-64' : '' }}">
                <!-- Navbar -->
                <x-navbar :activePage="$title ?? 'Dashboard'" :adminName="Auth::user()->name ?? 'Admin'" />

                <!-- Mobile Menu (Bottom/Top) - could be extracted later if needed -->
                
                <!-- Main Content -->
                <main class="flex-1 min-w-0 overflow-y-auto p-4 md:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
        <!-- Toast Notification Popup System -->
        <script>
            window.showToast = function(title, message, type = 'success') {
                const existingToast = document.getElementById('toastNotification');
                if (existingToast) {
                    existingToast.remove();
                }

                const toast = document.createElement('div');
                toast.id = 'toastNotification';
                toast.className = 'fixed top-6 right-6 z-[100] max-w-sm w-full transform translate-x-full opacity-0 transition-all duration-500 ease-out pointer-events-none';
                toast.setAttribute('role', 'alert');

                const isSuccess = type === 'success';
                const bgClass = isSuccess ? 'bg-white border-green-200' : 'bg-white border-red-200';
                const shadowClass = isSuccess ? 'shadow-green-600/10' : 'shadow-red-600/10';
                const iconBg = isSuccess ? 'bg-green-100' : 'bg-red-100';
                const iconColor = isSuccess ? 'text-green-600' : 'text-red-600';
                const progressBg = isSuccess ? 'from-green-400 to-emerald-500' : 'from-red-400 to-rose-500';
                
                const iconSvg = isSuccess 
                    ? `<svg class="w-5 h-5 ${iconColor}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`
                    : `<svg class="w-5 h-5 ${iconColor}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>`;

                toast.innerHTML = `
                    <div class="${bgClass} border rounded-2xl shadow-2xl ${shadowClass} overflow-hidden">
                        <div class="flex items-start gap-3 p-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full ${iconBg} flex items-center justify-center">
                                ${iconSvg}
                            </div>
                            <div class="flex-1 min-w-0 pt-0.5">
                                <p class="text-sm font-bold text-gray-900">${title}</p>
                                <p class="text-sm text-gray-600 mt-0.5">${message}</p>
                            </div>
                            <button onclick="dismissToast()" class="flex-shrink-0 p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="h-1 bg-gray-100">
                            <div id="toastProgress" class="h-full bg-gradient-to-r ${progressBg} rounded-full transition-all ease-linear" style="width: 100%"></div>
                        </div>
                    </div>
                `;

                document.body.appendChild(toast);

                const progress = toast.querySelector('#toastProgress');
                const duration = 4000;

                setTimeout(() => {
                    toast.classList.remove('translate-x-full', 'opacity-0', 'pointer-events-none');
                    toast.classList.add('translate-x-0', 'opacity-100', 'pointer-events-auto');
                }, 100);

                setTimeout(() => {
                    progress.style.transition = 'width ' + duration + 'ms linear';
                    progress.style.width = '0%';
                }, 200);

                if (window.toastTimeout) clearTimeout(window.toastTimeout);
                window.toastTimeout = setTimeout(() => {
                    dismissToast();
                }, duration + 200);
            };

            window.dismissToast = function() {
                const toast = document.getElementById('toastNotification');
                if (toast) {
                    toast.classList.add('translate-x-full', 'opacity-0');
                    toast.classList.remove('pointer-events-auto');
                    if (window.toastTimeout) clearTimeout(window.toastTimeout);
                    setTimeout(() => toast.remove(), 500);
                }
            };

            // Trigger session success toast if present
            @if(session('success'))
                document.addEventListener('DOMContentLoaded', function() {
                    showToast('Berhasil!', "{{ session('success') }}", 'success');
                });
            @endif
        </script>

        <script>
            // Global Form Submit Loading Handler
            document.addEventListener('submit', function(e) {
                const form = e.target;
                
                // Only process forms with the 'form-with-loading' class
                if (form.classList.contains('form-with-loading')) {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    
                    if (submitBtn) {
                        const text = submitBtn.querySelector('.btn-text');
                        const icon = submitBtn.querySelector('.btn-icon');
                        const spinner = submitBtn.querySelector('.btn-spinner');
                        
                        // Disable button to prevent multiple submissions
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                        
                        // Show loading state if elements exist
                        if (text) text.innerText = 'Loading...';
                        if (icon) icon.classList.add('hidden');
                        if (spinner) spinner.classList.remove('hidden');
                    }
                }
            });
        </script>
    </body>
</html>
