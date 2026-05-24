<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Dashboard' }} | SISCARD</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-[#121212] text-gray-900 dark:text-gray-100 transition-colors">
        <div class="flex min-h-screen overflow-x-hidden bg-gradient-to-br from-indigo-50/50 via-white to-blue-50/30 dark:from-[#121212] dark:via-[#1A1A1A] dark:to-[#121212] transition-colors">
            @if(Auth::user() && Auth::user()->role === 'admin')
                <!-- Sidebar Backdrop -->
                <div id="sidebarBackdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-40 transition-opacity duration-300 opacity-0 pointer-events-none md:hidden"></div>
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
                toast.className = 'fixed top-4 right-4 left-4 md:left-auto md:right-6 md:top-6 z-[100] w-auto md:w-full md:max-w-sm transform translate-x-full opacity-0 transition-all duration-500 ease-out pointer-events-none';
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

            // Toggle Sidebar for Mobile/Responsive View
            window.toggleSidebar = function() {
                const sidebar = document.getElementById('sidebarMenu');
                const backdrop = document.getElementById('sidebarBackdrop');
                
                if (!sidebar || !backdrop) return;
                
                const isOpen = sidebar.classList.contains('translate-x-0');
                
                if (isOpen) {
                    sidebar.classList.remove('translate-x-0');
                    sidebar.classList.add('-translate-x-full');
                    
                    backdrop.classList.remove('opacity-100', 'pointer-events-auto');
                    backdrop.classList.add('opacity-0', 'pointer-events-none');
                    document.body.classList.remove('overflow-hidden');
                } else {
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.classList.add('translate-x-0');
                    
                    backdrop.classList.remove('opacity-0', 'pointer-events-none');
                    backdrop.classList.add('opacity-100', 'pointer-events-auto');
                    document.body.classList.add('overflow-hidden');
                }
            };

            // Close sidebar on ESC key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    const sidebar = document.getElementById('sidebarMenu');
                    if (sidebar && sidebar.classList.contains('translate-x-0')) {
                        toggleSidebar();
                    }
                }
            });

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

        <!-- Global Premium Delete Confirmation Modal -->
        <div id="globalConfirmDeleteModal" onclick="if(event.target === this) closeConfirmDeleteModal()" class="fixed inset-0 z-[150] hidden bg-gray-900/50 backdrop-blur-sm transition-opacity duration-300 opacity-0 items-center justify-center p-4">
            <div id="globalConfirmDeleteContent" class="bg-white rounded-2xl p-6 shadow-2xl max-w-sm w-full border border-gray-100 flex flex-col items-center text-center transform scale-95 opacity-0 transition-all duration-300">
                <div class="w-12 h-12 bg-red-50 text-red-600 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                </div>
                <h4 class="text-lg font-bold text-gray-900">Hapus Data?</h4>
                <p class="text-sm text-gray-500 mt-2 leading-relaxed">Apakah Anda yakin ingin menghapus data ini? Tindakan ini bersifat permanen dan tidak dapat dibatalkan.</p>
                <div class="grid grid-cols-2 gap-3 w-full mt-6">
                    <button type="button" id="confirmDeleteCancelBtn" onclick="closeConfirmDeleteModal()" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-all cursor-pointer">
                        Batal
                    </button>
                    <button type="button" id="confirmDeleteSubmitBtn" onclick="executePendingDelete()" class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl transition-all cursor-pointer shadow-lg shadow-red-600/10 flex items-center justify-center gap-2">
                        <span class="btn-text">Hapus</span>
                        <svg class="btn-spinner animate-spin h-3.5 w-3.5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <script>
            // Global custom confirm delete interceptor
            document.addEventListener('submit', function(event) {
                const form = event.target;
                const methodInput = form.querySelector('input[name="_method"]');
                
                const isDeleteAction = (methodInput && methodInput.value.toUpperCase() === 'DELETE') || 
                                       (form.action && (form.action.includes('bulk-delete') || form.action.includes('destroy') || form.action.includes('delete')));
                
                if (isDeleteAction) {
                    if (form.dataset.confirmed === 'true') {
                        return;
                    }
                    
                    event.preventDefault();
                    window.pendingDeleteForm = form;
                    
                    const modal = document.getElementById('globalConfirmDeleteModal');
                    const content = document.getElementById('globalConfirmDeleteContent');
                    
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    setTimeout(() => {
                        modal.classList.remove('opacity-0');
                        content.classList.remove('scale-95', 'opacity-0');
                    }, 10);
                }
            });

            window.executePendingDelete = function() {
                if (window.pendingDeleteForm) {
                    const submitBtn = document.getElementById('confirmDeleteSubmitBtn');
                    const cancelBtn = document.getElementById('confirmDeleteCancelBtn');
                    
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                        const text = submitBtn.querySelector('.btn-text');
                        const spinner = submitBtn.querySelector('.btn-spinner');
                        if (text) text.innerText = 'Hapus...';
                        if (spinner) spinner.classList.remove('hidden');
                    }
                    if (cancelBtn) {
                        cancelBtn.disabled = true;
                        cancelBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        cancelBtn.removeAttribute('onclick');
                    }

                    window.pendingDeleteForm.dataset.confirmed = 'true';
                    window.pendingDeleteForm.submit();
                }
            };

            window.closeConfirmDeleteModal = function() {
                const modal = document.getElementById('globalConfirmDeleteModal');
                const content = document.getElementById('globalConfirmDeleteContent');
                
                modal.classList.add('opacity-0');
                content.classList.add('scale-95', 'opacity-0');
                
                setTimeout(() => {
                    modal.classList.remove('flex');
                    modal.classList.add('hidden');
                    window.pendingDeleteForm = null;
                    
                    // Reset modal buttons state
                    const submitBtn = document.getElementById('confirmDeleteSubmitBtn');
                    const cancelBtn = document.getElementById('confirmDeleteCancelBtn');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                        const text = submitBtn.querySelector('.btn-text');
                        const spinner = submitBtn.querySelector('.btn-spinner');
                        if (text) text.innerText = 'Hapus';
                        if (spinner) spinner.classList.add('hidden');
                    }
                    if (cancelBtn) {
                        cancelBtn.disabled = false;
                        cancelBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        cancelBtn.setAttribute('onclick', 'closeConfirmDeleteModal()');
                    }
                }, 300);
            };
        </script>
    </body>
</html>
