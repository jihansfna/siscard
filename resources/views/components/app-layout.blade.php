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
            <!-- Sidebar -->
            <x-sidebar />

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 md:ml-64">
                <!-- Navbar -->
                <x-navbar :activePage="$title ?? 'Dashboard'" :adminName="Auth::user()->name ?? 'Admin'" />

                <!-- Mobile Menu (Bottom/Top) - could be extracted later if needed -->
                
                <!-- Main Content -->
                <main class="flex-1 min-w-0 overflow-y-auto p-4 md:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
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
