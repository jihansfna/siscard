<x-app-layout>
    <x-slot name="title">Master Karyawan</x-slot>

    <section class="text-gray-800 dark:text-gray-200 transition-colors">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Data Master Karyawan</h2>

            <div class="flex flex-wrap items-center gap-3">
                <form class="flex items-center gap-2" method="GET" action="{{ route('dashboard.employees.index') }}">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                        </div>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / badge..." class="pl-10 pr-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500 transition-colors">
                    </div>
                </form>

                {{-- Export Dropdown --}}
                <div class="relative" id="exportDropdownContainer">
                    <button type="button" onclick="toggleDropdown('exportDropdown')" class="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-emerald-600/20 active:scale-95">
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                        <span>Ekspor</span>
                        <x-heroicon-o-chevron-down class="w-3 h-3" />
                    </button>
                    <div id="exportDropdown" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 py-1 z-50 hidden transition-all">
                        <a href="{{ route('dashboard.export.employees.excel') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-[#2A2A2A] transition-colors">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM9.5 11.5l2 3.5-2 3.5h1.5l1.25-2.5L13.5 18.5H15l-2-3.5 2-3.5h-1.5l-1.25 2.5-1.25-2.5H9.5z"/></svg>
                            <span>Ekspor Excel</span>
                        </a>
                        <a href="{{ route('dashboard.export.employees.pdf') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-[#2A2A2A] transition-colors">
                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM10.5 11c-.83 0-1.5.67-1.5 1.5v4c0 .83.67 1.5 1.5 1.5h3c.83 0 1.5-.67 1.5-1.5v-4c0-.83-.67-1.5-1.5-1.5h-3z"/></svg>
                            <span>Ekspor PDF</span>
                        </a>
                    </div>
                </div>


                <button type="button" onclick="document.getElementById('addEmployeeModal').classList.remove('hidden'); document.getElementById('addEmployeeModal').classList.add('flex')" class="flex items-center gap-3 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-primary-600/20 active:scale-95">
                    <span>Tambah Karyawan</span>
                    <x-heroicon-o-user-plus class="w-4 h-4" />
                </button>
            </div>
        </div>

        <!-- Bulk Delete Actions Bar -->
        <div id="bulkDeleteBar" class="hidden items-center justify-between bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800/50 rounded-xl p-4 mb-4 transition-all duration-300">
            <div class="flex items-center gap-2 text-red-700 dark:text-red-400 text-sm font-semibold">
                <x-heroicon-o-trash class="w-5 h-5 text-red-500" />
                <span id="selectedCount">0</span> data terpilih
            </div>
            <button type="submit" form="bulkDeleteForm" class="flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-bold transition-all shadow-md shadow-red-600/20 active:scale-95">
                <span>Hapus Terpilih</span>
            </button>
        </div>

        <form id="bulkDeleteForm" action="{{ route('dashboard.employees.bulk_destroy') }}" method="POST" class="hidden">
            @csrf
        </form>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-md shadow-gray-200/60 dark:shadow-none transition-all">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-400">
                    <thead class="bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300 text-xs uppercase font-semibold border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-4 py-3 w-10">
                                <x-checkbox id="selectAllEmployees" onclick="toggleSelectAllEmployees(this)" />
                            </th>
                            <th class="px-4 py-3">Badge</th>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Departemen</th>
                            <th class="px-4 py-3">Jabatan</th>
                            <th class="px-4 py-3">Tanggal Masuk</th>
                            <th class="px-4 py-3">Tanggal Keluar</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @forelse ($employees as $employee)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-[#2A2A2A] transition-colors">
                                <td class="px-4 py-3">
                                    <x-checkbox name="ids[]" value="{{ $employee->id }}" form="bulkDeleteForm" class="employee-checkbox" onclick="updateBulkDeleteBar()" />
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $employee->badge }}</td>
                                <td class="px-4 py-3">{{ $employee->nama }}</td>
                                <td class="px-4 py-3">{{ $employee->departemen ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $employee->jabatan ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $employee->tanggal_masuk?->format('d M Y') ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if($employee->tanggal_keluar)
                                        @if(!$employee->aktif)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">{{ $employee->tanggal_keluar->format('d M Y') }}</span>
                                        @else
                                            <span>{{ $employee->tanggal_keluar->format('d M Y') }}</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if(!$employee->aktif)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700/30 dark:text-gray-400">{{ $employee->status }}</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">{{ $employee->status }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right space-x-1">
                                    @if($employee->aktif)
                                        <!-- Edit Employee Button -->
                                        <button type="button" onclick="document.getElementById('editEmployeeModal-{{ $employee->id }}').classList.remove('hidden'); document.getElementById('editEmployeeModal-{{ $employee->id }}').classList.add('flex')" class="inline-flex p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Ubah Karyawan">
                                            <x-heroicon-o-pencil-square class="w-5 h-5" />
                                        </button>

                                        <!-- Set Inactive Button (only if active) -->
                                        <button type="button" onclick="document.getElementById('inactiveEmployeeModal-{{ $employee->id }}').classList.remove('hidden'); document.getElementById('inactiveEmployeeModal-{{ $employee->id }}').classList.add('flex')" class="inline-flex p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Set Tidak Aktif">
                                            <x-heroicon-o-no-symbol class="w-5 h-5" />
                                        </button>

                                        <!-- Inactive Confirmation Modal -->
                                        <div id="inactiveEmployeeModal-{{ $employee->id }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 backdrop-blur-sm text-left">
                                            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden flex flex-col transform scale-100">
                                                <div class="p-6">
                                                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mb-4 mx-auto">
                                                        <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-red-600" />
                                                    </div>
                                                    <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Nonaktifkan Karyawan?</h3>
                                                    <p class="text-sm text-gray-500 text-center">
                                                        Apakah Anda yakin ingin menonaktifkan <strong>{{ $employee->nama }}</strong>? Akses sistem akan dicabut dan status anggota akan otomatis menjadi Tidak Aktif.
                                                    </p>
                                                </div>
                                                <div class="px-6 py-4 bg-gray-50 flex justify-center gap-3">
                                                    <button type="button" onclick="document.getElementById('inactiveEmployeeModal-{{ $employee->id }}').classList.add('hidden'); document.getElementById('inactiveEmployeeModal-{{ $employee->id }}').classList.remove('flex')" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-xl transition-all">
                                                        Batal
                                                    </button>
                                                    <form action="{{ route('dashboard.employees.set_inactive', $employee->id) }}" method="POST" class="m-0 form-with-loading">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl shadow-lg shadow-red-600/20 transition-all flex items-center gap-2">
                                                            <span class="btn-text">Ya, Nonaktifkan</span>
                                                            <svg class="btn-spinner animate-spin h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Edit Employee Modal -->
                                    <div id="editEmployeeModal-{{ $employee->id }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/50 backdrop-blur-sm text-left">
                                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden max-h-[90vh] flex flex-col border border-transparent dark:border-gray-700">
                                            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex-shrink-0">
                                                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Ubah Karyawan: {{ $employee->nama }}</h3>
                                                <button type="button" onclick="document.getElementById('editEmployeeModal-{{ $employee->id }}').classList.add('hidden'); document.getElementById('editEmployeeModal-{{ $employee->id }}').classList.remove('flex')" class="text-gray-400 hover:text-gray-600 transition-colors">
                                                    <x-heroicon-o-x-mark class="w-6 h-6" />
                                                </button>
                                            </div>
                                            <div class="overflow-y-auto p-6 text-left">
                                                <form action="{{ route('dashboard.employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6 form-with-loading" id="editEmployeeForm-{{ $employee->id }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                        <div class="space-y-2">
                                                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Badge ID <span class="text-red-500">*</span></label>
                                                            <input name="badge" type="text" value="{{ old('badge', $employee->badge) }}" required autocomplete="off"
                                                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm dark:text-white dark:placeholder-gray-500">
                                                        </div>

                                                        <div class="space-y-2">
                                                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Nama Lengkap <span class="text-red-500">*</span></label>
                                                            <input name="nama" type="text" value="{{ old('nama', $employee->nama) }}" required 
                                                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm dark:text-white dark:placeholder-gray-500">
                                                        </div>

                                                        <div class="space-y-2">
                                                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Departemen</label>
                                                            <input name="departemen" type="text" value="{{ old('departemen', $employee->departemen) }}" 
                                                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm dark:text-white dark:placeholder-gray-500">
                                                        </div>

                                                        <div class="space-y-2">
                                                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Nomor Telepon (WA)</label>
                                                            <input name="nomor_telp" type="text" value="{{ old('nomor_telp', $employee->nomor_telp) }}" placeholder="Contoh: 08123456789"
                                                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm dark:text-white dark:placeholder-gray-500">
                                                        </div>

                                                        <div class="space-y-2">
                                                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Jabatan</label>
                                                            <input name="jabatan" type="text" value="{{ old('jabatan', $employee->jabatan) }}" 
                                                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm dark:text-white dark:placeholder-gray-500">
                                                        </div>

                                                        <div class="space-y-2">
                                                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Line</label>
                                                            <input name="line" type="text" value="{{ old('line', $employee->line) }}" 
                                                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm dark:text-white dark:placeholder-gray-500">
                                                        </div>

                                                        <div class="space-y-2">
                                                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Tanggal Masuk</label>
                                                            <x-datepicker id="tanggal_masuk_edit_{{ $employee->id }}" name="tanggal_masuk" value="{{ old('tanggal_masuk', $employee->tanggal_masuk?->format('Y-m-d')) }}" />
                                                        </div>

                                                        <div class="space-y-2">
                                                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Tanggal Keluar</label>
                                                            <x-datepicker id="tanggal_keluar_edit_{{ $employee->id }}" name="tanggal_keluar" value="{{ old('tanggal_keluar', $employee->tanggal_keluar?->format('Y-m-d')) }}" />
                                                            <p class="text-xs text-gray-500 mt-1">Kosongkan jika karyawan masih aktif</p>
                                                        </div>

                                                        <div class="space-y-2">
                                                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Tempat Lahir</label>
                                                            <input name="tempat_lahir" type="text" value="{{ old('tempat_lahir', $employee->tempat_lahir) }}" 
                                                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm dark:text-white dark:placeholder-gray-500">
                                                        </div>

                                                        <div class="space-y-2">
                                                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Tanggal Lahir</label>
                                                            <x-datepicker id="tanggal_lahir_edit_{{ $employee->id }}" name="tanggal_lahir" value="{{ old('tanggal_lahir', $employee->tanggal_lahir?->format('Y-m-d')) }}" />
                                                        </div>

                                                        @if($employee->foto)
                                                            <div class="space-y-2 md:col-span-2">
                                                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Foto Karyawan</label>
                                                                <div class="mb-2">
                                                                    <img src="{{ asset('storage/' . $employee->foto) }}" alt="Photo" class="w-20 h-20 object-cover rounded-lg border border-gray-200">
                                                                </div>
                                                                <input name="foto" type="file" accept=".jpg,.jpeg,.png" onchange="handleFileChange(this)"
                                                                    class="block w-full text-sm text-gray-500 border border-gray-200 rounded-xl cursor-pointer bg-gray-50 dark:text-gray-400 dark:bg-gray-900 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent file:cursor-pointer file:bg-primary-50 dark:file:bg-primary-900/30 file:border-0 file:border-r file:border-solid file:border-gray-200 dark:file:border-gray-700 file:!mr-4 file:!py-3 file:!px-5 dark:file:text-primary-400 file:!text-primary-700 file:!font-bold hover:file:bg-primary-100 dark:hover:file:bg-primary-900/50 transition-all">
                                                                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, JPEG. Maks: 2MB</p>
                                                            </div>
                                                        @else
                                                            <div class="space-y-2">
                                                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Foto Karyawan</label>
                                                                <input name="foto" type="file" accept=".jpg,.jpeg,.png" onchange="handleFileChange(this)"
                                                                    class="block w-full text-sm text-gray-500 border border-gray-200 rounded-xl cursor-pointer bg-gray-50 dark:text-gray-400 dark:bg-gray-900 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent file:cursor-pointer file:bg-primary-50 dark:file:bg-primary-900/30 file:border-0 file:border-r file:border-solid file:border-gray-200 dark:file:border-gray-700 file:!mr-4 file:!py-3 file:!px-5 dark:file:text-primary-400 file:!text-primary-700 file:!font-bold hover:file:bg-primary-100 dark:hover:file:bg-primary-900/50 transition-all">
                                                                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, JPEG. Maks: 2MB</p>
                                                            </div>
                                                        @endif

                                                        <div class="space-y-2 md:col-span-2">
                                                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Alamat</label>
                                                            <textarea name="alamat" rows="3" placeholder="Alamat lengkap..." 
                                                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm resize-none dark:text-white dark:placeholder-gray-500">{{ old('alamat', $employee->alamat) }}</textarea>
                                                        </div>
                                                    </div>

                                                    {{-- Pertanyaan Keamanan --}}
                                                    @php $userRecord = \App\Models\User::where('badge', $employee->badge)->first(); @endphp
                                                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                                        <div class="flex items-center gap-2 mb-4">
                                                            <x-heroicon-o-shield-check class="w-5 h-5 text-primary-600" />
                                                            <h3 class="text-base font-bold text-gray-800 dark:text-white">Pertanyaan Keamanan</h3>
                                                            @if ($userRecord && $userRecord->pertanyaan_rahasia)
                                                                <span class="ml-auto inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                                                    <x-heroicon-m-check-circle class="w-3.5 h-3.5" />
                                                                    Sudah diatur
                                                                </span>
                                                            @else
                                                                <span class="ml-auto inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                                                    <x-heroicon-m-exclamation-triangle class="w-3.5 h-3.5" />
                                                                    Belum diatur
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <p class="text-xs text-gray-500 mb-4">Digunakan untuk verifikasi identitas saat pemulihan kata sandi. Jawaban akan dienkripsi.</p>
                                                        
                                                        <div class="grid grid-cols-1 gap-6">
                                                            <div class="space-y-2 group">
                                                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Pertanyaan Rahasia</label>
                                                                <input name="pertanyaan_rahasia" type="text" 
                                                                    value="{{ old('pertanyaan_rahasia', $userRecord->pertanyaan_rahasia ?? '') }}" 
                                                                    placeholder="Contoh: Apa nama hewan peliharaan pertama saya?" maxlength="500"
                                                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm dark:text-white dark:placeholder-gray-500">
                                                            </div>

                                                            <div class="space-y-2 group">
                                                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Jawaban Rahasia</label>
                                                                <input name="jawaban_rahasia" type="password" autocomplete="new-password"
                                                                    placeholder="{{ ($userRecord && $userRecord->jawaban_rahasia) ? 'Kosongkan jika tidak ingin mengubah' : 'Masukkan jawaban rahasia' }}" maxlength="255"
                                                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm dark:text-white dark:placeholder-gray-500">
                                                                @if ($userRecord && $userRecord->jawaban_rahasia)
                                                                    <p class="text-xs text-gray-400 mt-1">Jawaban tersimpan terenkripsi. Isi ulang hanya jika ingin mengubah.</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex justify-end gap-3 flex-shrink-0">
                                                <button type="button" onclick="document.getElementById('editEmployeeModal-{{ $employee->id }}').classList.add('hidden'); document.getElementById('editEmployeeModal-{{ $employee->id }}').classList.remove('flex')" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-200 rounded-lg transition-colors">
                                                    Batal
                                                </button>
                                                <button type="submit" form="editEmployeeForm-{{ $employee->id }}" class="px-6 py-2.5 text-sm font-bold bg-primary-600 hover:bg-primary-700 text-white rounded-xl shadow-lg shadow-primary-600/20 transition-all flex items-center gap-2">
                                                    <span class="btn-text">Perbarui</span>
                                                    <svg class="btn-spinner animate-spin h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <x-heroicon-o-users class="w-10 h-10 text-gray-300 mb-3" />
                                        <p>Tidak ada data karyawan tersedia.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($employees->hasPages() || $employees->total() > 0)
            <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                <x-custom-pagination :paginator="$employees" />
            </div>
            @endif
        </div>
    </section>

    <!-- Add Employee Modal -->
    <div id="addEmployeeModal" class="fixed inset-0 z-50 {{ $errors->any() ? 'flex' : 'hidden' }} items-center justify-center bg-gray-900/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden max-h-[90vh] flex flex-col border border-transparent dark:border-gray-700">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex-shrink-0">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Tambah Karyawan Baru</h3>
                <button type="button" onclick="document.getElementById('addEmployeeModal').classList.add('hidden'); document.getElementById('addEmployeeModal').classList.remove('flex')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
            <div class="overflow-y-auto p-6">
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-semibold flex items-start gap-3">
                        <x-heroicon-s-exclamation-circle class="w-5 h-5 flex-shrink-0 mt-0.5" />
                        <div class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form action="{{ route('dashboard.employees.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 form-with-loading" id="addEmployeeForm">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2 group">
                            <label for="badge" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Badge ID <span class="text-red-500">*</span></label>
                            <input id="badge" name="badge" type="text" value="{{ old('badge') }}" placeholder="Contoh: 12345" required autocomplete="off"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white dark:focus:bg-[#2A2A2A] transition-all text-sm dark:text-white dark:placeholder-gray-500">
                        </div>

                        <div class="space-y-2 group">
                            <label for="nama" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input id="nama" name="nama" type="text" value="{{ old('nama') }}" placeholder="Nama Karyawan" required 
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white dark:focus:bg-[#2A2A2A] transition-all text-sm dark:text-white dark:placeholder-gray-500">
                        </div>

                        <div class="space-y-2 group">
                            <label for="departemen" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Departemen</label>
                            <input id="departemen" name="departemen" type="text" value="{{ old('departemen') }}" placeholder="Contoh: IT" 
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white dark:focus:bg-[#2A2A2A] transition-all text-sm dark:text-white dark:placeholder-gray-500">
                        </div>

                        <div class="space-y-2 group">
                            <label for="nomor_telp" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Nomor Telepon (WA)</label>
                            <input id="nomor_telp" name="nomor_telp" type="text" value="{{ old('nomor_telp') }}" placeholder="Contoh: 08123456789" 
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white dark:focus:bg-[#2A2A2A] transition-all text-sm dark:text-white dark:placeholder-gray-500">
                        </div>

                        <div class="space-y-2 group">
                            <label for="jabatan" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Jabatan</label>
                            <input id="jabatan" name="jabatan" type="text" value="{{ old('jabatan') }}" placeholder="Contoh: Staff" 
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white dark:focus:bg-[#2A2A2A] transition-all text-sm dark:text-white dark:placeholder-gray-500">
                        </div>

                        <div class="space-y-2 group">
                            <label for="line" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Line</label>
                            <input id="line" name="line" type="text" value="{{ old('line') }}" placeholder="Contoh: Line 1" 
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white dark:focus:bg-[#2A2A2A] transition-all text-sm dark:text-white dark:placeholder-gray-500">
                        </div>

                        <div class="space-y-2 group">
                            <label for="tanggal_masuk" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Tanggal Masuk</label>
                            <x-datepicker id="tanggal_masuk" name="tanggal_masuk" value="{{ old('tanggal_masuk') }}" />
                        </div>

                        <div class="space-y-2 group">
                            <label for="tanggal_keluar" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Tanggal Keluar</label>
                            <x-datepicker id="tanggal_keluar" name="tanggal_keluar" value="{{ old('tanggal_keluar') }}" />
                            <p class="text-xs text-gray-500 mt-1">Kosongkan jika karyawan masih aktif</p>
                        </div>

                        <div class="space-y-2 group">
                            <label for="tempat_lahir" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Tempat Lahir</label>
                            <input id="tempat_lahir" name="tempat_lahir" type="text" value="{{ old('tempat_lahir') }}" placeholder="Contoh: Batam" 
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white dark:focus:bg-[#2A2A2A] transition-all text-sm dark:text-white dark:placeholder-gray-500">
                        </div>

                        <div class="space-y-2 group">
                            <label for="tanggal_lahir" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Tanggal Lahir</label>
                            <x-datepicker id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" />
                        </div>

                        <div class="space-y-2 group">
                            <label for="foto" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Foto Karyawan</label>
                            <input id="foto" name="foto" type="file" accept=".jpg,.jpeg,.png" onchange="handleFileChange(this)"
                                class="block w-full text-sm text-gray-500 border border-gray-200 rounded-xl cursor-pointer bg-gray-50 dark:text-gray-400 dark:bg-gray-900 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent file:cursor-pointer file:bg-primary-50 dark:file:bg-primary-900/30 file:border-0 file:border-r file:border-solid file:border-gray-200 dark:file:border-gray-700 file:!mr-4 file:!py-3 file:!px-5 dark:file:text-primary-400 file:!text-primary-700 file:!font-bold hover:file:bg-primary-100 dark:hover:file:bg-primary-900/50 transition-all">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, JPEG. Maks: 2MB</p>
                        </div>

                        <div class="space-y-2 group md:col-span-2">
                            <label for="alamat" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Alamat</label>
                            <textarea id="alamat" name="alamat" rows="3" placeholder="Alamat lengkap..." 
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white dark:focus:bg-[#2A2A2A] transition-all text-sm resize-none dark:text-white dark:placeholder-gray-500">{{ old('alamat') }}</textarea>
                        </div>
                    </div>

                    {{-- Pertanyaan Keamanan --}}
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <div class="flex items-center gap-2 mb-4">
                            <x-heroicon-o-shield-check class="w-5 h-5 text-primary-600" />
                            <h3 class="text-base font-bold text-gray-800 dark:text-white">Pertanyaan Keamanan</h3>
                        </div>
                        <p class="text-xs text-gray-500 mb-4">Digunakan untuk verifikasi identitas saat pemulihan kata sandi. Jawaban akan dienkripsi dan tidak dapat dilihat oleh siapapun.</p>
                        
                        <div class="grid grid-cols-1 gap-6">
                            <div class="space-y-2 group">
                                <label for="pertanyaan_rahasia" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Pertanyaan Rahasia</label>
                                <input id="pertanyaan_rahasia" name="pertanyaan_rahasia" type="text" value="{{ old('pertanyaan_rahasia') }}" 
                                    placeholder="Contoh: Apa nama hewan peliharaan pertama saya?" maxlength="500"
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white dark:focus:bg-[#2A2A2A] transition-all text-sm dark:text-white dark:placeholder-gray-500">
                            </div>

                            <div class="space-y-2 group">
                                <label for="jawaban_rahasia" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Jawaban Rahasia</label>
                                <input id="jawaban_rahasia" name="jawaban_rahasia" type="password" value="{{ old('jawaban_rahasia') }}" autocomplete="new-password"
                                    placeholder="Masukkan jawaban rahasia" maxlength="255"
                                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white dark:focus:bg-[#2A2A2A] transition-all text-sm dark:text-white dark:placeholder-gray-500">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex justify-end gap-3 flex-shrink-0">
                <button type="button" onclick="document.getElementById('addEmployeeModal').classList.add('hidden'); document.getElementById('addEmployeeModal').classList.remove('flex')" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-200 rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit" form="addEmployeeForm" class="px-6 py-2.5 text-sm font-bold bg-primary-600 hover:bg-primary-700 text-white rounded-xl shadow-lg shadow-primary-600/20 transition-all flex items-center gap-2">
                    <span class="btn-text">Simpan Karyawan</span>
                    <svg class="btn-spinner animate-spin h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <script>
        function toggleSelectAllEmployees(source) {
            const checkboxes = document.querySelectorAll('.employee-checkbox');
            checkboxes.forEach(cb => cb.checked = source.checked);
            updateBulkDeleteBar();
        }

        function updateBulkDeleteBar() {
            const checkboxes = document.querySelectorAll('.employee-checkbox');
            const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
            const bar = document.getElementById('bulkDeleteBar');
            const countSpan = document.getElementById('selectedCount');
            const selectAll = document.getElementById('selectAllEmployees');

            if (checkedCount > 0) {
                bar.classList.remove('hidden');
                bar.classList.add('flex');
                countSpan.textContent = checkedCount;
            } else {
                bar.classList.add('hidden');
                bar.classList.remove('flex');
            }

            if (selectAll) {
                selectAll.checked = (checkedCount === checkboxes.length && checkboxes.length > 0);
            }
        }

        function handleFileChange(input) {
            if (input.files && input.files.length > 0) {
                const file = input.files[0];
                const fileName = file.name;
                const fileSize = file.size; // in bytes
                
                // Get extension
                const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
                
                // 1. Validate Extension
                if (!allowedExtensions.exec(fileName)) {
                    // Reset input
                    input.value = '';
                    
                    // Highlight input in RED
                    input.classList.remove('bg-gray-50', 'border-gray-200', 'focus:ring-primary-500', 'bg-green-50/40', 'border-green-500', 'focus:ring-green-500', 'ring-green-500/20');
                    input.classList.add('bg-red-50/40', 'border-red-500', 'focus:ring-red-500', 'ring-2', 'ring-red-500/20');
                    
                    // Trigger beautiful error toast
                    if (window.showToast) {
                        window.showToast('Invalid Format!', 'Only JPG, PNG, and JPEG formats are allowed.', 'error');
                    }
                    return;
                }
                
                // 2. Validate Size (Max 2MB = 2 * 1024 * 1024 bytes)
                const maxSize = 2 * 1024 * 1024;
                if (fileSize > maxSize) {
                    // Reset input
                    input.value = '';
                    
                    // Highlight input in RED
                    input.classList.remove('bg-gray-50', 'border-gray-200', 'focus:ring-primary-500', 'bg-green-50/40', 'border-green-500', 'focus:ring-green-500', 'ring-green-500/20');
                    input.classList.add('bg-red-50/40', 'border-red-500', 'focus:ring-red-500', 'ring-2', 'ring-red-500/20');
                    
                    // Trigger beautiful error toast
                    if (window.showToast) {
                        window.showToast('File Too Large!', 'Maximum photo size is 2MB.', 'error');
                    }
                    return;
                }
                
                // If a file is selected and valid, apply a beautiful premium green theme
                input.classList.remove('bg-gray-50', 'border-gray-200', 'focus:ring-primary-500', 'bg-red-50/40', 'border-red-500', 'focus:ring-red-500', 'ring-red-500/20');
                input.classList.add('bg-green-50/40', 'border-green-500', 'focus:ring-green-500', 'ring-2', 'ring-green-500/20');
            } else {
                // Reset to original styling
                input.classList.add('bg-gray-50', 'border-gray-200', 'focus:ring-primary-500');
                input.classList.remove('bg-green-50/40', 'border-green-500', 'focus:ring-green-500', 'ring-2', 'ring-green-500/20', 'bg-red-50/40', 'border-red-500', 'focus:ring-red-500', 'ring-red-500/20');
            }
        }
    </script>

    {{-- Import Employee Modal --}}
    <div id="importEmployeeModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col border border-transparent dark:border-gray-700">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex-shrink-0">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Impor Data Karyawan</h3>
                <button type="button" onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
            <div class="p-6">
                @if(session('import_errors'))
                    <div class="mb-4 p-4 rounded-xl bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm">
                        <p class="font-bold mb-1">Beberapa baris dilewati:</p>
                        <ul class="list-disc list-inside space-y-0.5 text-xs">
                            @foreach(session('import_errors') as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-5">
                    <div class="flex items-start gap-3">
                        <x-heroicon-o-information-circle class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" />
                        <div class="text-sm text-blue-800">
                            <p class="font-bold mb-1">Instruksi Impor:</p>
                            <ul class="list-disc list-inside space-y-0.5 text-xs">
                                <li>Unduh template terlebih dahulu</li>
                                <li>Isi data sesuai dengan format template</li>
                                <li>Kolom <strong>Badge</strong> dan <strong>Nama</strong> wajib diisi</li>
                                <li>Format tanggal: <strong>dd/mm/yyyy</strong></li>
                                <li>Badge yang sudah terdaftar otomatis akan dilewati</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <a href="{{ route('dashboard.import.employees.template') }}" class="flex items-center justify-center gap-2 w-full px-4 py-2.5 mb-5 bg-gray-100 dark:bg-gray-900 hover:bg-gray-200 dark:hover:bg-[#2A2A2A] text-gray-700 dark:text-gray-300 rounded-xl text-sm font-bold transition-all border border-gray-200 dark:border-gray-700">
                    <x-heroicon-o-document-arrow-down class="w-4 h-4" />
                    <span>Unduh Template Excel</span>
                </a>

                <form action="{{ route('dashboard.import.employees') }}" method="POST" enctype="multipart/form-data" class="form-with-loading" id="importEmployeeForm">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Berkas Excel (.xlsx) <span class="text-red-500">*</span></label>
                            <div id="importDropZone" class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center hover:border-primary-400 dark:hover:border-primary-500 transition-colors cursor-pointer bg-gray-50 dark:bg-gray-900" onclick="document.getElementById('importFileInput').click()">
                                <input type="file" name="file" id="importFileInput" accept=".xlsx,.xls" required class="hidden" onchange="handleImportFileChange(this)">
                                <x-heroicon-o-cloud-arrow-up class="w-10 h-10 text-gray-300 mx-auto mb-2" />
                                <p class="text-sm text-gray-500 font-medium" id="importFileName">Klik atau seret berkas ke sini</p>
                                <p class="text-xs text-gray-400 mt-1">Format: .xlsx atau .xls (maks 5MB)</p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex justify-end gap-3 flex-shrink-0">
                <button type="button" onclick="closeImportModal()" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-200 rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit" form="importEmployeeForm" class="px-6 py-2.5 text-sm font-bold bg-amber-500 hover:bg-amber-600 text-white rounded-xl shadow-lg shadow-amber-500/20 transition-all flex items-center gap-2">
                    <span class="btn-text">Impor Data</span>
                    <svg class="btn-spinner animate-spin h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <script>
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const container = document.getElementById('exportDropdownContainer');
            if (container && !container.contains(e.target)) {
                document.getElementById('exportDropdown').classList.add('hidden');
            }
        });

        function closeImportModal() {
            document.getElementById('importEmployeeModal').classList.add('hidden');
            document.getElementById('importEmployeeModal').classList.remove('flex');
        }

        function handleImportFileChange(input) {
            const fileNameEl = document.getElementById('importFileName');
            const dropZone = document.getElementById('importDropZone');
            if (input.files && input.files.length > 0) {
                const file = input.files[0];
                const ext = file.name.split('.').pop().toLowerCase();
                
                if (!['xlsx', 'xls'].includes(ext)) {
                    input.value = '';
                    fileNameEl.textContent = 'File format not supported!';
                    fileNameEl.classList.add('text-red-500');
                    dropZone.classList.add('border-red-400');
                    dropZone.classList.remove('border-gray-300', 'border-green-400');
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    input.value = '';
                    fileNameEl.textContent = 'File too large! Max 5MB';
                    fileNameEl.classList.add('text-red-500');
                    dropZone.classList.add('border-red-400');
                    dropZone.classList.remove('border-gray-300', 'border-green-400');
                    return;
                }

                fileNameEl.textContent = file.name;
                fileNameEl.classList.remove('text-gray-500', 'text-red-500');
                fileNameEl.classList.add('text-green-700', 'font-bold');
                dropZone.classList.add('border-green-400', 'bg-green-50/30');
                dropZone.classList.remove('border-gray-300', 'border-red-400');
            } else {
                fileNameEl.textContent = 'Click or drag file here';
                fileNameEl.classList.remove('text-green-700', 'font-bold', 'text-red-500');
                fileNameEl.classList.add('text-gray-500');
                dropZone.classList.remove('border-green-400', 'bg-green-50/30', 'border-red-400');
                dropZone.classList.add('border-gray-300');
            }
        }

        // Open import modal if there are import errors
        @if(session('import_errors'))
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('importEmployeeModal').classList.remove('hidden');
                document.getElementById('importEmployeeModal').classList.add('flex');
            });
        @endif
    </script>
</x-app-layout>

