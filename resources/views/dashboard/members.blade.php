<x-app-layout>
    <x-slot name="title">Members</x-slot>

    <section class="text-gray-800 dark:text-gray-200 transition-colors">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Manajemen Anggota</h2>

            <div class="flex flex-wrap items-center gap-3">
                <form class="flex items-center gap-2" method="GET" action="{{ route('dashboard.members') }}">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                        </div>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau badge..." class="pl-10 pr-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500 transition-colors">
                        @if(request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        @if(request('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif
                        @if(request('perPage'))
                            <input type="hidden" name="perPage" value="{{ request('perPage') }}">
                        @endif
                    </div>
                </form>

                {{-- Filter Button --}}
                <button type="button" onclick="openFilterModal()" class="flex items-center gap-2 px-4 py-2 border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-[#2A2A2A] text-gray-700 dark:text-gray-300 rounded-lg text-sm font-semibold transition-all shadow-sm active:scale-95">
                    <x-heroicon-o-funnel class="w-4 h-4" />
                    <span>Filter</span>
                </button>

                {{-- Export Dropdown --}}
                <div class="relative" id="exportDropdownContainer">
                    <button type="button" onclick="toggleDropdown('exportDropdown')" class="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-emerald-600/20 active:scale-95">
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                        <span>Ekspor</span>
                        <x-heroicon-o-chevron-down class="w-3 h-3" />
                    </button>
                    <div id="exportDropdown" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 py-1 z-50 hidden transition-all">
                        <a href="{{ route('dashboard.export.members.excel') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-[#2A2A2A] transition-colors">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM9.5 11.5l2 3.5-2 3.5h1.5l1.25-2.5L13.5 18.5H15l-2-3.5 2-3.5h-1.5l-1.25 2.5-1.25-2.5H9.5z"/></svg>
                            <span>Ekspor Excel</span>
                        </a>
                        <a href="{{ route('dashboard.export.members.pdf') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-[#2A2A2A] transition-colors">
                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM10.5 11c-.83 0-1.5.67-1.5 1.5v4c0 .83.67 1.5 1.5 1.5h3c.83 0 1.5-.67 1.5-1.5v-4c0-.83-.67-1.5-1.5-1.5h-3z"/></svg>
                            <span>Ekspor PDF</span>
                        </a>
                    </div>
                </div>

                <button type="button" onclick="openAddMemberModal()" class="flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm shadow-primary-600/30">
                    <span>Tambah Anggota</span>
                    <x-heroicon-o-user-plus class="w-4 h-4" />
                </button>
            </div>
        </div>
        
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

        <!-- Bulk Delete Actions Bar -->
        <div id="bulkDeleteBar" class="hidden items-center justify-between bg-red-50 border border-red-100 rounded-xl p-4 mb-4 transition-all duration-300">
            <div class="flex items-center gap-2 text-red-700 text-sm font-semibold">
                <x-heroicon-o-trash class="w-5 h-5 text-red-500" />
                <span id="selectedCount">0</span> data terpilih
            </div>
            <button type="submit" form="bulkDeleteForm" class="flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-bold transition-all shadow-md shadow-red-600/20 active:scale-95">
                <span>Hapus Terpilih</span>
            </button>
        </div>

        <form id="bulkDeleteForm" action="{{ route('dashboard.members.bulk_destroy') }}" method="POST" class="hidden">
            @csrf
        </form>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-md shadow-gray-200/60 dark:shadow-none transition-all">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-400">
                    <thead class="bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300 text-xs uppercase font-semibold border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-4 py-3 w-10">
                                <x-checkbox id="selectAllMembers" onclick="toggleSelectAllMembers(this)" />
                            </th>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Karyawan</th>
                            <th class="px-4 py-3">Jabatan</th>
                            <th class="px-4 py-3">Departemen</th>
                            <th class="px-4 py-3">Peran</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @forelse ($members as $index => $member)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-[#2A2A2A] transition-colors">
                                <td class="px-4 py-3">
                                    <x-checkbox name="ids[]" value="{{ $member->id }}" form="bulkDeleteForm" class="member-checkbox" onclick="updateBulkDeleteBar()" />
                                </td>
                                <td class="px-4 py-3">{{ $members->firstItem() + $index }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $member->karyawan->nama }}</div>
                                    <div class="text-xs text-gray-500">{{ $member->karyawan->badge }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $member->karyawan->jabatan ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $member->karyawan->departemen ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/25 dark:text-blue-300">
                                        {{ $member->jabatan->nama ?? 'Anggota' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($member->status == 'registered')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Terdaftar</span>
                                    @elseif($member->status == 'pending')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">Menunggu Verifikasi</span>
                                    @elseif($member->status == 'inactive')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700/30 dark:text-gray-400">Tidak Aktif</span>
                                    @elseif($member->status == 'rejected')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Ditolak</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700/30 dark:text-gray-400">{{ ucfirst($member->status) }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $memberData = [
                                            'id' => $member->id,
                                            'name' => $member->karyawan->nama,
                                            'badge' => $member->karyawan->badge,
                                            'status' => $member->status,
                                            'uuid' => $member->uuid,
                                            'created_at' => $member->created_at->format('d F Y, H:i'),
                                            'added_by' => 'Admin',
                                            'image' => $member->karyawan->foto,
                                            'birth_place' => $member->karyawan->tempat_lahir ?? '-',
                                            'birth_date' => $member->karyawan->tanggal_lahir?->format('d F Y') ?? '-',
                                            'join_date' => $member->karyawan->tanggal_masuk?->format('d F Y') ?? '-',
                                            'address' => $member->karyawan->alamat ?? '-',
                                            'role' => $member->jabatan->nama ?? 'Anggota',
                                            'member_role_id' => $member->jabatan_id,
                                            'sign_image' => $member->tanda_tangan,
                                            'verify_token' => $member->verify_token,
                                            'card_download_url' => route('dashboard.members.card.download', $member->id),
                                            'update_url' => route('dashboard.members.update', $member->id),
                                            'logs_url' => route('dashboard.members.logs', $member->id),
                                        ];
                                    @endphp
                                    <button class="inline-flex p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View Member Details" data-member="{{ json_encode($memberData) }}" onclick="openMemberDetail(this)">
                                        <x-heroicon-o-eye class="w-5 h-5" />
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <x-heroicon-o-users class="w-10 h-10 text-gray-300 mb-3" />
                                        <p>Tidak ada data anggota tersedia.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($members->hasPages() || $members->total() > 0)
            <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                <x-custom-pagination :paginator="$members" />
            </div>
            @endif
        </div>
    </section>

    <!-- Add Member Modal -->
    <div id="addMemberModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-gray-900/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-4xl overflow-hidden max-h-[90vh] flex flex-col border border-transparent dark:border-gray-700">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex-shrink-0">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Tambah Anggota Baru</h3>
                <button type="button" onclick="closeAddMemberModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
            <form action="{{ route('dashboard.members.store') }}" method="POST" id="addMemberForm" class="flex flex-col overflow-hidden form-with-loading" onsubmit="return validateAddMemberForm(event, this)">
                @csrf
                <div class="p-6 overflow-y-auto">
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Pilih karyawan di bawah ini untuk ditambahkan sebagai Anggota. Hanya karyawan aktif yang bukan anggota yang ditampilkan.</p>
                    </div>
                    
                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <table class="w-full text-left text-sm text-gray-600 dark:text-gray-400">
                            <thead class="bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300 text-xs uppercase font-semibold border-b border-gray-200 dark:border-gray-700 sticky top-0">
                                <tr>
                                    <th class="px-4 py-3 w-10">
                                        <x-checkbox id="selectAllEmployees" onclick="toggleSelectAll(this)" />
                                    </th>
                                    <th class="px-4 py-3">No</th>
                                    <th class="px-4 py-3">Karyawan</th>
                                    <th class="px-4 py-3">Jabatan</th>
                                    <th class="px-4 py-3">Departemen</th>
                                    <th class="px-4 py-3">Kode Line</th>
                                    <th class="px-4 py-3">Tanggal Keluar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                @forelse ($availableEmployees as $index => $emp)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-[#2A2A2A] transition-colors available-employee-row">
                                        <td class="px-4 py-3">
                                            <x-checkbox name="employee_ids[]" value="{{ $emp->id }}" class="employee-checkbox" onclick="updateSelectAllHeaderState()" />
                                        </td>
                                        <td class="px-4 py-3">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $emp->nama }}</div>
                                            <div class="text-xs text-gray-500">{{ $emp->badge }}</div>
                                        </td>
                                        <td class="px-4 py-3">{{ $emp->jabatan ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $emp->departemen ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $emp->line ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            @if($emp->tanggal_keluar)
                                                <span class="text-xs text-gray-600">{{ $emp->tanggal_keluar->format('d M Y') }}</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                            <p>Semua karyawan sudah terdaftar sebagai anggota atau tidak aktif.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <!-- Client-side pagination for available employees in modal -->
                        <div class="flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-700 hidden" id="availableEmployeesPagination">
                            <span class="text-xs text-gray-500 font-medium" id="availPageInfo">Menampilkan 1 hingga 5 dari 9 karyawan</span>
                            <div class="flex gap-2">
                                <button type="button" onclick="changeAvailPage(-1)" id="availPrevBtn" class="px-3 py-1.5 text-xs font-bold bg-white border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all">Sebelumnya</button>
                                <button type="button" onclick="changeAvailPage(1)" id="availNextBtn" class="px-3 py-1.5 text-xs font-bold bg-white border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all">Selanjutnya</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex justify-end gap-3 flex-shrink-0">
                    <button type="button" onclick="closeAddMemberModal()" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-200 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 text-sm font-bold bg-primary-600 hover:bg-primary-700 text-white rounded-xl shadow-lg shadow-primary-600/20 transition-all flex items-center gap-2">
                        <span class="btn-text">Tambah Anggota</span>
                        <svg class="btn-spinner animate-spin h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Filter Members Modal -->
    <div id="filterMembersModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/50 backdrop-blur-sm p-4 transition-opacity">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-sm flex flex-col border border-transparent dark:border-gray-700">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Filter Anggota</h3>
                <button type="button" onclick="closeFilterModal()" class="text-gray-400 hover:text-gray-600 transition-colors cursor-pointer">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
            
            <form action="{{ route('dashboard.members') }}" method="GET" class="m-0">
                <!-- Keep search query if it exists -->
                @if(request('q'))
                    <input type="hidden" name="q" value="{{ request('q') }}">
                @endif
                @if(request('perPage'))
                    <input type="hidden" name="perPage" value="{{ request('perPage') }}">
                @endif
                
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <div class="relative custom-dropdown-container">
                            <input type="hidden" name="status" id="statusFilterInput" value="{{ request('status', 'Semua Status') }}">
                            <button type="button" onclick="toggleCustomDropdown('statusDropdownMenu')" class="w-full flex items-center justify-between pl-4 pr-3 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors text-gray-700 dark:text-white cursor-pointer shadow-sm">
                                <span id="statusFilterBtnText">{{ request('status', 'Semua Status') }}</span>
                                <svg class="w-4 h-4 text-gray-400 pointer-events-none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/></svg>
                            </button>
                            <div id="statusDropdownMenu" class="custom-dropdown-menu absolute z-50 hidden mt-1.5 w-full bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-lg">
                                <ul class="py-1 text-sm text-gray-700 dark:text-gray-300 font-medium">
                                    <li><button type="button" onclick="selectDropdownOption('statusFilterInput', 'statusFilterBtnText', 'statusDropdownMenu', 'Semua Status', 'Semua Status')" class="inline-flex items-center w-full px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-[#2A2A2A] transition-colors">Semua Status</button></li>
                                    <li><button type="button" onclick="selectDropdownOption('statusFilterInput', 'statusFilterBtnText', 'statusDropdownMenu', 'Anggota Terdaftar', 'Anggota Terdaftar')" class="inline-flex items-center w-full px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-[#2A2A2A] transition-colors">Anggota Terdaftar</button></li>
                                    <li><button type="button" onclick="selectDropdownOption('statusFilterInput', 'statusFilterBtnText', 'statusDropdownMenu', 'Menunggu Verifikasi', 'Menunggu Verifikasi')" class="inline-flex items-center w-full px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-[#2A2A2A] transition-colors">Menunggu Verifikasi</button></li>
                                    <li><button type="button" onclick="selectDropdownOption('statusFilterInput', 'statusFilterBtnText', 'statusDropdownMenu', 'Tidak Aktif', 'Tidak Aktif')" class="inline-flex items-center w-full px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-[#2A2A2A] transition-colors">Tidak Aktif</button></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Urutkan Berdasarkan</label>
                        <div class="relative custom-dropdown-container">
                            <input type="hidden" name="sort" id="sortFilterInput" value="{{ request('sort', 'desc') }}">
                            <button type="button" onclick="toggleCustomDropdown('sortDropdownMenu')" class="w-full flex items-center justify-between pl-4 pr-3 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors text-gray-700 dark:text-white cursor-pointer shadow-sm">
                                <span id="sortFilterBtnText">{{ request('sort', 'desc') === 'desc' ? 'Data Terbaru' : 'Data Terlama' }}</span>
                                <svg class="w-4 h-4 text-gray-400 pointer-events-none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/></svg>
                            </button>
                            <div id="sortDropdownMenu" class="custom-dropdown-menu absolute z-50 hidden mt-1.5 w-full bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-lg">
                                <ul class="py-1 text-sm text-gray-700 dark:text-gray-300 font-medium">
                                    <li><button type="button" onclick="selectDropdownOption('sortFilterInput', 'sortFilterBtnText', 'sortDropdownMenu', 'desc', 'Data Terbaru')" class="inline-flex items-center w-full px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-[#2A2A2A] transition-colors">Data Terbaru</button></li>
                                    <li><button type="button" onclick="selectDropdownOption('sortFilterInput', 'sortFilterBtnText', 'sortDropdownMenu', 'asc', 'Data Terlama')" class="inline-flex items-center w-full px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-[#2A2A2A] transition-colors">Data Terlama</button></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 flex items-center justify-between border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-b-2xl">
                    <a href="{{ route('dashboard.members') }}{{ request('q') ? '?q=' . request('q') : '' }}" class="text-sm font-bold text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                        Reset Filter
                    </a>
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-primary-600/20 active:scale-95 cursor-pointer">
                        Kirim
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentAvailPage = 1;
        const availPageSize = 5;

        function initAvailPagination() {
            const rows = document.querySelectorAll('.available-employee-row');
            const totalRows = rows.length;
            const totalPages = Math.ceil(totalRows / availPageSize);
            const paginationContainer = document.getElementById('availableEmployeesPagination');
            
            if (!paginationContainer) return;
            
            if (totalRows <= availPageSize) {
                paginationContainer.classList.add('hidden');
                rows.forEach(row => row.classList.remove('hidden'));
                updateSelectAllHeaderState();
                return;
            }
            
            paginationContainer.classList.remove('hidden');
            paginationContainer.classList.add('flex');
            
            rows.forEach((row, index) => {
                const start = (currentAvailPage - 1) * availPageSize;
                const end = start + availPageSize;
                if (index >= start && index < end) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
            
            const startIdx = (currentAvailPage - 1) * availPageSize + 1;
            const endIdx = Math.min(startIdx + availPageSize - 1, totalRows);
            document.getElementById('availPageInfo').textContent = `Menampilkan ${startIdx} hingga ${endIdx} dari ${totalRows} karyawan`;
            
            document.getElementById('availPrevBtn').disabled = (currentAvailPage === 1);
            document.getElementById('availNextBtn').disabled = (currentAvailPage === totalPages);
            
            updateSelectAllHeaderState();
        }

        function changeAvailPage(direction) {
            currentAvailPage += direction;
            initAvailPagination();
        }

        document.addEventListener('DOMContentLoaded', () => {
            initAvailPagination();
        });

        function toggleSelectAll(source) {
            const rows = document.querySelectorAll('.available-employee-row');
            rows.forEach(row => {
                if (!row.classList.contains('hidden')) {
                    const cb = row.querySelector('.employee-checkbox');
                    if (cb) {
                        cb.checked = source.checked;
                    }
                }
            });
        }

        function updateSelectAllHeaderState() {
            const rows = document.querySelectorAll('.available-employee-row');
            let allVisibleChecked = true;
            let hasVisible = false;
            
            rows.forEach(row => {
                if (!row.classList.contains('hidden')) {
                    hasVisible = true;
                    const cb = row.querySelector('.employee-checkbox');
                    if (cb && !cb.checked) {
                        allVisibleChecked = false;
                    }
                }
            });
            
            const selectAll = document.getElementById('selectAllEmployees');
            if (selectAll) {
                selectAll.checked = hasVisible ? allVisibleChecked : false;
            }
        }

        function openAddMemberModal() {
            resetAddMemberModal();
            document.getElementById('addMemberModal').classList.remove('hidden');
        }

        function closeAddMemberModal() {
            document.getElementById('addMemberModal').classList.add('hidden');
            resetAddMemberModal();
        }

        function resetAddMemberModal() {
            const checkboxes = document.querySelectorAll('.employee-checkbox');
            checkboxes.forEach(cb => cb.checked = false);
            
            const selectAll = document.getElementById('selectAllEmployees');
            if (selectAll) {
                selectAll.checked = false;
            }
            
            currentAvailPage = 1;
            initAvailPagination();
        }

        function validateAddMemberForm(e, form) {
            const checked = form.querySelectorAll('.employee-checkbox:checked');
            if (checked.length === 0) {
                e.preventDefault();
                
                // Show notification
                if (typeof window.showToast === 'function') {
                    window.showToast('Gagal!', 'Pilih karyawan terlebih dahulu!', 'error');
                } else {
                    alert('Pilih karyawan terlebih dahulu!');
                }
                
                // Keep the loading effect briefly, then reset so user can try again
                setTimeout(() => {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                        const text = submitBtn.querySelector('.btn-text');
                        const spinner = submitBtn.querySelector('.btn-spinner');
                        if (text) text.innerText = 'Tambah Anggota';
                        if (spinner) spinner.classList.add('hidden');
                    }
                }, 500);
                
                return false;
            }
            return true;
        }

        function toggleSelectAllMembers(source) {
            const checkboxes = document.querySelectorAll('.member-checkbox');
            checkboxes.forEach(cb => cb.checked = source.checked);
            updateBulkDeleteBar();
        }

        function updateBulkDeleteBar() {
            const checkboxes = document.querySelectorAll('.member-checkbox');
            const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
            const bar = document.getElementById('bulkDeleteBar');
            const countSpan = document.getElementById('selectedCount');
            const selectAll = document.getElementById('selectAllMembers');

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

        function switchMemberTab(tab) {
            const infoBtn = document.getElementById('tabInfoBtn');
            const historyBtn = document.getElementById('tabHistoryBtn');
            const infoContent = document.getElementById('tabInfoContent');
            const historyContent = document.getElementById('tabHistoryContent');

            if (tab === 'info') {
                infoBtn.className = 'py-3 text-sm font-bold text-primary-600 border-b-2 border-primary-600';
                historyBtn.className = 'py-3 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent';
                infoContent.classList.remove('hidden');
                historyContent.classList.add('hidden');
            } else {
                historyBtn.className = 'py-3 text-sm font-bold text-primary-600 border-b-2 border-primary-600';
                infoBtn.className = 'py-3 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent';
                historyContent.classList.remove('hidden');
                infoContent.classList.add('hidden');
            }
        }

        function openMemberDetail(btn) {
            const member = JSON.parse(btn.dataset.member);
            
            document.getElementById('detailNameTop').textContent = member.name;
            document.getElementById('detailBadgeTop').textContent = member.badge;
            document.getElementById('detailName').textContent = member.name;
            document.getElementById('detailBadge').textContent = member.badge;
            document.getElementById('detailAddedInfo').textContent = 'Ditambahkan oleh ' + member.added_by + ', ' + member.created_at;
            
            const statusColors = {
                'registered': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                'inactive': 'bg-gray-100 text-gray-800 dark:bg-gray-700/30 dark:text-gray-400',
                'rejected': 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
            };
            const statusLabels = {
                'registered': 'Terdaftar',
                'pending': 'Menunggu Verifikasi',
                'inactive': 'Tidak Aktif',
                'rejected': 'Ditolak'
            };
            const sColor = statusColors[member.status] || 'bg-gray-100 text-gray-800';
            const sLabel = statusLabels[member.status] || member.status;
            
            document.getElementById('detailStatusBadgeTop').className = 'inline-flex items-center px-2 py-1 rounded text-xs font-semibold ' + sColor;
            document.getElementById('detailStatusBadgeTop').textContent = sLabel;
            document.getElementById('detailStatusBadge').className = 'inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold ' + sColor;
            document.getElementById('detailStatusBadge').textContent = sLabel;
            
            // Profile image
            const detailImg = document.getElementById('detailImage');
            const detailImgFallback = document.getElementById('detailImageFallback');
            const detailImgLoading = document.getElementById('detailImageLoading');
            
            detailImg.classList.add('hidden');
            detailImgFallback.classList.add('hidden');

            if (member.image) {
                detailImgLoading.classList.remove('hidden');
                detailImg.onload = function() {
                    detailImgLoading.classList.add('hidden');
                    detailImg.classList.remove('hidden');
                };
                detailImg.src = '/storage/' + member.image;
            } else {
                detailImgLoading.classList.add('hidden');
                detailImgFallback.classList.remove('hidden');
                detailImgFallback.textContent = member.name.charAt(0).toUpperCase();
            }
            
            // QR Code — lazy-loaded via route
            const qrUrl = "{{ route('qr.image') }}" + "?data=" + encodeURIComponent(window.location.origin + '/verify/' + member.verify_token) + "&size=400";
            const detailQrCode = document.getElementById('detailQrCode');
            const detailQrCodeLoading = document.getElementById('detailQrCodeLoading');
            
            detailQrCode.classList.add('hidden');
            detailQrCodeLoading.classList.remove('hidden');
            
            detailQrCode.onload = function() {
                detailQrCodeLoading.classList.add('hidden');
                detailQrCode.classList.remove('hidden');
            };
            detailQrCode.src = qrUrl;
            
            document.getElementById('detailBirth').textContent = member.birth_place + ', ' + member.birth_date;
            document.getElementById('detailJoinDate').textContent = member.join_date;
            document.getElementById('detailRole').textContent = member.role;
            document.getElementById('detailAddress').textContent = member.address;
            
            // Signature image display
            const detailSignImage = document.getElementById('detailSignImage');
            const detailSign = document.getElementById('detailSign');
            const detailSignLoading = document.getElementById('detailSignLoading');
            
            detailSignImage.classList.add('hidden');
            detailSign.classList.add('hidden');

            if (member.sign_image) {
                detailSignLoading.classList.remove('hidden');
                detailSignImage.onload = function() {
                    detailSignLoading.classList.add('hidden');
                    detailSignImage.classList.remove('hidden');
                };
                detailSignImage.src = '/storage/' + member.sign_image;
            } else {
                detailSignLoading.classList.add('hidden');
                detailSign.textContent = '-';
                detailSign.classList.remove('hidden');
            }
            
            // Store member data for edit modal
            currentMemberData = member;
            
            // Download card button & dynamic status message
            const downloadBtn = document.getElementById('downloadCardBtn');
            const msgContainer = document.getElementById('cardNotRegisteredMsg');
            const msgText = document.getElementById('cardNotRegisteredMsgText');
            
            if (member.status === 'registered') {
                downloadBtn.href = member.card_download_url;
                downloadBtn.classList.remove('opacity-50', 'pointer-events-none');
                msgContainer.classList.add('hidden');
                document.getElementById('cardPreviewContainer').classList.remove('opacity-40');
            } else {
                downloadBtn.href = '#';
                downloadBtn.classList.add('opacity-50', 'pointer-events-none');
                msgContainer.classList.remove('hidden');
                document.getElementById('cardPreviewContainer').classList.add('opacity-40');
                
                // Set custom message based on status
                if (member.status === 'pending') {
                    msgText.textContent = 'Menunggu konfirmasi admin. Kartu digital belum dapat dibuat.';
                    msgContainer.className = 'mb-4 p-4 border rounded-xl text-sm font-semibold flex items-center gap-3 bg-blue-50 border-blue-200 text-blue-800';
                } else if (member.status === 'inactive') {
                    msgText.textContent = 'Anggota tidak aktif. Akses kartu digital dicabut.';
                    msgContainer.className = 'mb-4 p-4 border rounded-xl text-sm font-semibold flex items-center gap-3 bg-red-50 border-red-200 text-red-800';
                } else if (member.status === 'rejected') {
                    msgText.textContent = 'Pendaftaran ditolak. Kartu digital tidak tersedia.';
                    msgContainer.className = 'mb-4 p-4 border rounded-xl text-sm font-semibold flex items-center gap-3 bg-red-50 border-red-200 text-red-800';
                } else {
                    msgText.textContent = 'Kartu digital belum dapat dibuat. Status anggota harus "Terdaftar".';
                    msgContainer.className = 'mb-4 p-4 border rounded-xl text-sm font-semibold flex items-center gap-3 bg-amber-50 border-amber-200 text-amber-800';
                }
            }
            
            // Card front preview - Photo
            const cardPhotoImg = document.getElementById('cardPhotoImg');
            const cardPhotoFallback = document.getElementById('cardPhotoFallback');
            const cardPhotoLoading = document.getElementById('cardPhotoLoading');
            
            cardPhotoImg.classList.add('hidden');
            cardPhotoFallback.classList.add('hidden');

            if (member.image) {
                cardPhotoLoading.classList.remove('hidden');
                cardPhotoImg.onload = function() {
                    cardPhotoLoading.classList.add('hidden');
                    cardPhotoImg.classList.remove('hidden');
                };
                cardPhotoImg.src = '/storage/' + member.image;
            } else {
                cardPhotoLoading.classList.add('hidden');
                cardPhotoFallback.classList.remove('hidden');
                cardPhotoFallback.textContent = member.name.charAt(0).toUpperCase();
            }
            
            // Card front QR
            const cardFrontQr = document.getElementById('cardFrontQr');
            const cardFrontQrLoading = document.getElementById('cardFrontQrLoading');
            
            cardFrontQr.classList.add('hidden');
            cardFrontQrLoading.classList.remove('hidden');
            
            cardFrontQr.onload = function() {
                cardFrontQrLoading.classList.add('hidden');
                cardFrontQr.classList.remove('hidden');
            };
            cardFrontQr.src = qrUrl;
            
            // Card data
            document.getElementById('cardBadge').textContent = member.badge;
            document.getElementById('cardName').textContent = member.name;
            document.getElementById('cardBirth').textContent = member.birth_place + ' / ' + member.birth_date;
            document.getElementById('cardAddress').textContent = member.address;

            // Ensure we open Info tab by default
            switchMemberTab('info');
            
            // Render History Timeline — lazy-loaded via AJAX
            const timelineContainer = document.getElementById('detailHistoryTimeline');
            timelineContainer.innerHTML = '<p class="text-sm text-gray-400 ml-4 animate-pulse">Memuat riwayat...</p>';
            
            fetch(member.logs_url)
                .then(res => res.json())
                .then(logs => {
                    timelineContainer.innerHTML = '';
                    
                    if (logs && logs.length > 0) {
                        const sortedLogs = [...logs].reverse();
                        
                        sortedLogs.forEach(log => {
                            const item = document.createElement('div');
                            item.className = 'relative pl-8';
                            item.innerHTML = `
                                <!-- Theme Dot -->
                                <div class="absolute w-4 h-4 bg-emerald-500 rounded-full -left-[9px] top-1 border-4 border-white dark:border-gray-900 shadow-sm shadow-emerald-500/30"></div>
                                
                                <!-- Content -->
                                <div class="flex flex-col sm:flex-row sm:items-start gap-x-6 gap-y-2">
                                    <!-- Date & Time -->
                                    <div class="w-32 flex-shrink-0 pt-0.5">
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">${log.created_at_date}</p>
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">${log.created_at_time}</p>
                                    </div>
                                    
                                    <!-- Main Detail -->
                                    <div class="flex-1">
                                        <h5 class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-2">${log.description}</h5>
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-emerald-600 flex items-center justify-center text-[10px] text-white font-bold overflow-hidden">
                                                ${log.actor_name.charAt(0).toUpperCase()}
                                            </div>
                                            <span class="text-xs text-gray-600 dark:text-gray-400 font-medium">${log.actor_name} ${log.actor_badge ? '(' + log.actor_badge + ')' : ''}</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                            timelineContainer.appendChild(item);
                        });
                    } else {
                        timelineContainer.innerHTML = '<p class="text-sm text-gray-500 ml-4">Belum ada riwayat aktivitas.</p>';
                    }
                })
                .catch(() => {
                    timelineContainer.innerHTML = '<p class="text-sm text-red-500 ml-4">Gagal memuat riwayat.</p>';
                });

            const drawer = document.getElementById('memberDetailDrawer');
            const content = document.getElementById('drawerContent');
            drawer.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('translate-x-full');
            }, 10);
        }

        function closeMemberDetail() {
            const drawer = document.getElementById('memberDetailDrawer');
            const content = document.getElementById('drawerContent');
            content.classList.add('translate-x-full');
            setTimeout(() => {
                drawer.classList.add('hidden');
            }, 300);
        }

        function openFilterModal() {
            document.getElementById('filterMembersModal').classList.remove('hidden');
            document.getElementById('filterMembersModal').classList.add('flex');
        }

        function closeFilterModal() {
            document.getElementById('filterMembersModal').classList.add('hidden');
            document.getElementById('filterMembersModal').classList.remove('flex');
        }

        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const exportContainer = document.getElementById('exportDropdownContainer');
            if (exportContainer && !exportContainer.contains(e.target)) {
                document.getElementById('exportDropdown').classList.add('hidden');
            }
        });
    </script>

    <!-- Member Detail Off-Canvas Modal -->
    <div id="memberDetailDrawer" onclick="if(event.target === this) closeMemberDetail()" class="fixed inset-0 z-50 hidden bg-gray-900/50 backdrop-blur-sm transition-opacity">
        <div class="fixed inset-y-0 right-0 w-full max-w-2xl bg-white dark:bg-gray-800 shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col border-l border-transparent dark:border-gray-700" id="drawerContent">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-white dark:bg-gray-800 z-10 sticky top-0">
                <div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white" id="detailNameTop">NAME</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400" id="detailBadgeTop">BADGE</p>
                    <div class="mt-2">
                        <span id="detailStatusBadgeTop" class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold"></span>
                    </div>
                    <p class="text-xs text-gray-400 mt-2" id="detailAddedInfo">Ditambahkan oleh ...</p>
                </div>
                <button type="button" onclick="closeMemberDetail()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors absolute top-4 right-4 cursor-pointer">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
            
            <!-- Tabs -->
            <div class="px-6 border-b border-gray-100 dark:border-gray-700 flex gap-6 bg-white dark:bg-gray-800 sticky top-[120px] z-10">
                <button type="button" onclick="switchMemberTab('info')" id="tabInfoBtn" class="py-3 text-sm font-bold text-primary-600 border-b-2 border-primary-600">Informasi</button>
                <button type="button" onclick="switchMemberTab('history')" id="tabHistoryBtn" class="py-3 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">Riwayat</button>
            </div>

            <!-- Body -->
            <div class="p-6 overflow-y-auto flex-1 bg-gray-50/50 dark:bg-gray-900" id="tabInfoContent">
                <h4 class="text-base font-bold text-gray-800 dark:text-white mb-4">Informasi Pengguna</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Photo & Basic -->
                    <div class="flex flex-col items-center text-center">
                        <div class="w-32 h-40 bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden shadow-md mb-4 border-2 border-white relative">
                            <div id="detailImageLoading" class="absolute inset-0 flex items-center justify-center bg-gray-100 dark:bg-gray-800 hidden">
                                <svg class="animate-spin h-6 w-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </div>
                            <img id="detailImage" src="" alt="Profile" class="w-full h-full object-cover hidden relative z-10">
                            <div id="detailImageFallback" class="w-full h-full flex items-center justify-center text-gray-400 text-4xl font-bold relative z-10 hidden"></div>
                        </div>
                        <h5 class="font-bold text-gray-900 dark:text-white text-lg" id="detailName">NAME</h5>
                        <p class="text-gray-600 dark:text-gray-400 font-medium" id="detailBadge">BADGE</p>
                    </div>
                    
                    <!-- QR Code -->
                    <div class="flex flex-col items-center justify-center relative">
                        <div id="detailQrCodeLoading" class="w-32 h-32 mb-2 rounded border border-gray-200 shadow-sm p-1 bg-gray-50 dark:bg-gray-800 flex items-center justify-center hidden">
                            <svg class="animate-spin h-6 w-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                        <img id="detailQrCode" src="" alt="QR" class="w-32 h-32 mb-2 rounded border border-gray-200 shadow-sm p-1 bg-white hidden">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8 mb-8">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Tempat & Tanggal Lahir</p>
                        <p class="font-bold text-gray-900 dark:text-white text-sm" id="detailBirth">-</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Status</p>
                        <div><span id="detailStatusBadge" class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold"></span></div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Tanggal Bergabung</p>
                        <p class="font-bold text-gray-900 dark:text-white text-sm" id="detailJoinDate">-</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Peran</p>
                        <p class="font-bold text-gray-900 dark:text-white text-sm" id="detailRole">-</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">PUK</p>
                        <p class="font-bold text-gray-900 dark:text-white text-sm" id="detailPuk">PT XYZ</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Alamat</p>
                        <p class="font-bold text-gray-900 dark:text-white text-sm" id="detailAddress">-</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Tanda Tangan</p>
                        <div id="detailSignContainer" class="relative">
                            <p class="font-bold text-gray-900 dark:text-white text-sm" id="detailSign">-</p>
                            <div id="detailSignLoading" class="mt-1 w-16 h-8 border border-gray-200 dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-800 flex items-center justify-center hidden">
                                <svg class="animate-spin h-4 w-4 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </div>
                            <img id="detailSignImage" src="" alt="Signature" class="hidden mt-1 max-w-[120px] max-h-[60px] border border-gray-200 dark:border-gray-700 rounded bg-white p-1">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <hr class="border-gray-200 dark:border-gray-700 mb-6">
                <div class="flex items-center gap-3 mb-8">
                    <button type="button" onclick="openEditMemberModal()" class="flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold transition-all shadow-lg shadow-primary-600/20 active:scale-95 cursor-pointer">
                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                        <span>Ubah Anggota</span>
                    </button>
                </div>

                <!-- Card Preview + Download -->
                <hr class="border-gray-200 dark:border-gray-700 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-base font-bold text-gray-800 dark:text-white">Pratinjau Kartu</h4>
                    <a id="downloadCardBtn" href="#" class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-sm font-bold transition-all shadow-lg shadow-emerald-600/20 active:scale-95 no-underline">
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                        <span>Unduh Kartu Digital</span>
                    </a>
                </div>
                
                <div id="cardNotRegisteredMsg" class="hidden mb-4 p-4 border rounded-xl text-sm font-semibold flex items-center gap-3 bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-700/30 text-amber-800 dark:text-amber-300">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5 flex-shrink-0" />
                    <span id="cardNotRegisteredMsgText">Kartu digital belum dapat di-generate. Status keanggotaan harus "Registered".</span>
                </div>

                <div id="cardPreviewContainer" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Card Front Preview -->
                    <div class="border border-gray-200 rounded-xl bg-white shadow-sm overflow-hidden p-4 relative h-52 flex items-center justify-center text-gray-800 dark:text-gray-800">
                        <div class="relative z-10 w-full">
                            <div class="text-center mb-2">
                                <p class="text-[10px] font-bold text-gray-800 leading-tight">KARTU ANGGOTA</p>
                                <p class="text-[10px] font-bold text-orange-500 leading-tight">DEWAN PIMPINAN CABANG</p>
                                <p class="text-xs font-bold text-gray-800 leading-tight">Federasi SP LEM SPSI - KOTA BATAM</p>
                                <p class="text-[8px] font-bold text-gray-700 leading-tight">(DPC FSP LEM - SPSI - BTM)</p>
                                <p class="text-[5px] text-gray-400 mt-0.5 leading-tight">(Branch Leader Executive Union Worker's Metal, Electronic and Machine Federation - All Indonesia Worker's Union)</p>
                            </div>
                            <p class="text-[7px] font-bold mt-1 mb-1">NO. KTA: <span id="cardBadge" class="text-blue-600"></span></p>
                            <div class="flex gap-2 items-start mt-1">
                                <div class="flex-shrink-0 relative">
                                    <div class="w-14 h-[70px] bg-gray-100 rounded-sm overflow-hidden border border-gray-200" id="cardFrontPhoto">
                                        <div id="cardPhotoLoading" class="absolute inset-0 flex items-center justify-center bg-gray-100 hidden">
                                            <svg class="animate-spin h-4 w-4 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </div>
                                        <img id="cardPhotoImg" src="" class="w-full h-full object-cover hidden relative z-10" alt="">
                                        <div id="cardPhotoFallback" class="w-full h-full flex items-center justify-center text-gray-500 font-bold text-lg relative z-10 hidden"></div>
                                    </div>
                                </div>
                                <div class="flex-1 flex flex-col items-center justify-center relative">
                                    <div id="cardFrontQrLoading" class="w-14 h-14 border border-gray-200 bg-gray-50 rounded flex items-center justify-center hidden">
                                        <svg class="animate-spin h-4 w-4 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    </div>
                                    <img id="cardFrontQr" src="" alt="QR" class="w-14 h-14 border border-gray-200 bg-white p-0.5 rounded hidden">
                                </div>
                                <div class="flex-shrink-0 flex flex-col items-center">
                                    <img src="{{ asset('logo_lem_spsi.jpg') }}" class="w-14 h-14 object-contain" alt="Logo LEM SPSI">
                                    <p class="text-[6px] text-center text-gray-500 mt-0.5 font-bold">SP LEM - SPSI</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Back Preview -->
                    <div class="border border-gray-200 rounded-xl bg-white shadow-sm p-4 relative h-52 flex flex-col justify-between text-gray-800 dark:text-gray-800">
                        <div class="flex justify-between items-center mb-1.5 border-b border-gray-100 pb-1.5">
                            <img src="{{ asset('logo_lem_spsi.jpg') }}" class="w-6 h-6 object-contain mb-0.5" alt="Logo LEM">
                            <div class="text-center">
                                <p class="text-[8px] font-bold">PIMPINAN UNIT KERJA</p>
                                <p class="text-[8px] font-bold">SP LEM SPSI</p>
                                <p class="text-[9px] font-bold">PT XYZ</p>
                            </div>
                            <img src="{{ asset('logo_kspsi.png') }}" class="w-6 h-6 object-contain mb-0.5" alt="Logo KSPSI">
                        </div>
                        <div class="space-y-0.5 flex-1" style="font-size: 6px;">
                            <div class="flex"><div class="w-14 font-semibold">Nama</div><div>: <span id="cardName"></span></div></div>
                            <div class="flex"><div class="w-14 font-semibold">Tempat/Tgl.Lahir</div><div>: <span id="cardBirth"></span></div></div>
                            <div class="flex"><div class="w-14 font-semibold">P.U.K</div><div>: PT XYZ</div></div>
                            <div class="flex"><div class="w-14 font-semibold">Alamat</div><div class="flex-1 truncate">: <span id="cardAddress"></span></div></div>
                        </div>
                        <div class="flex mt-1">
                            <div class="w-[45%]"></div>
                            <div class="w-[55%]">
                                <p class="text-center text-gray-500 mb-0.5" style="font-size: 5px;">Batam,</p>
                                <div class="flex justify-around">
                                    <div class="text-[8px] text-center">
                                        <div id="cardKetuaSign" class="h-5 flex items-center justify-center">
                                            @if(isset($ketua) && $ketua->sign_image)
                                                <img id="cardKetuaSignImg" src="{{ asset('storage/' . $ketua->sign_image) }}" class="max-h-4" alt="TTD Ketua">
                                            @else
                                                <img id="cardKetuaSignImg" src="" class="max-h-4 hidden" alt="">
                                            @endif
                                        </div>
                                        <p class="text-gray-800" style="font-size: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" id="cardKetuaName">{{ isset($ketua) ? ($ketua->karyawan->nama ?? $ketua->employee->name ?? '') : '' }}</p>
                                        <div class="border-b border-gray-300 w-[90%] mx-auto mt-0.5 mb-0.5"></div>
                                        <p class="text-gray-600" style="font-size: 5px;">Ketua</p>
                                    </div>
                                    <div class="text-[8px] text-center">
                                        <div id="cardSekretarisSign" class="h-5 flex items-center justify-center">
                                            @if(isset($sekretaris) && $sekretaris->sign_image)
                                                <img id="cardSekretarisSignImg" src="{{ asset('storage/' . $sekretaris->sign_image) }}" class="max-h-4" alt="TTD Sekretaris">
                                            @else
                                                <img id="cardSekretarisSignImg" src="" class="max-h-4 hidden" alt="">
                                            @endif
                                        </div>
                                        <p class="text-gray-800" style="font-size: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" id="cardSekretarisName">{{ isset($sekretaris) ? ($sekretaris->karyawan->nama ?? $sekretaris->employee->name ?? '') : '' }}</p>
                                        <div class="border-b border-gray-300 w-[90%] mx-auto mt-0.5 mb-0.5"></div>
                                        <p class="text-gray-600" style="font-size: 5px;">Sekretaris</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between text-xs text-gray-500 mt-2 px-2">
                    <span>Tampak Depan</span>
                    <span>Tampak Belakang</span>
                </div>
            </div>

            <!-- History Tab Content -->
            <div class="p-6 overflow-y-auto flex-1 bg-gray-50/50 dark:bg-gray-900 hidden" id="tabHistoryContent">
                <div class="relative border-l-2 border-emerald-500 dark:border-emerald-500/40 ml-4 py-2 space-y-8" id="detailHistoryTimeline">
                    <!-- Logs will be injected here via JS -->
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Member Popup Modal -->
    <div id="editMemberModal" onclick="if(event.target === this) closeEditMemberModal()" class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-gray-900/60 backdrop-blur-sm transition-opacity">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md transform scale-95 opacity-0 transition-all duration-200 border border-transparent dark:border-gray-700" id="editModalContent">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Ubah Anggota</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" id="editModalSubtitle">-</p>
                </div>
                <button type="button" onclick="closeEditMemberModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors cursor-pointer">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            <!-- Modal Body -->
            <form id="editMemberForm" method="POST" enctype="multipart/form-data" class="form-with-loading">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-5">
                    <!-- Role Selector -->
                    <div>
                        <label for="editRole" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Peran <span class="text-red-500">*</span></label>
                        <div class="relative custom-dropdown-container">
                            <input type="hidden" name="jabatan_id" id="editRole" value="">
                            <button type="button" onclick="toggleCustomDropdown('editRoleDropdownMenu')" class="w-full flex items-center justify-between px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors text-gray-700 dark:text-white cursor-pointer shadow-sm">
                                <span id="editRoleBtnText" class="truncate font-medium">Pilih Peran</span>
                                <svg class="w-4 h-4 text-gray-400 pointer-events-none flex-shrink-0 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/></svg>
                            </button>
                            <div id="editRoleDropdownMenu" class="custom-dropdown-menu absolute z-50 hidden mt-1.5 w-full bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                                <ul class="py-1 text-sm text-gray-700 dark:text-gray-300 font-medium">
                                    @foreach($memberRoles as $role)
                                        <li>
                                            <button type="button" 
                                                onclick="selectEditRole('{{ $role->id }}', '{{ $role->nama }}')" 
                                                class="inline-flex flex-col items-start w-full px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-[#2A2A2A] transition-colors text-left">
                                                <span>{{ $role->nama }}</span>
                                                @if($role->penandatangan || $role->tunggal)
                                                    <span class="text-[10px] text-gray-400 font-normal mt-0.5">
                                                        @if($role->penandatangan) Tanda tangan di kartu @endif
                                                        @if($role->penandatangan && $role->tunggal) | @endif
                                                        @if($role->tunggal) Maks 1 orang @endif
                                                    </span>
                                                @endif
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <p class="text-[11px] text-amber-600 dark:text-amber-400 mt-1.5 font-medium" id="editRoleHint">Pilih peran untuk anggota ini.</p>
                    </div>

                    <!-- Signature Upload -->
                    <div id="signatureUploadSection">
                        <label for="editSignImage" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Unggah Tanda Tangan</label>
                        
                        <!-- Current Signature Preview -->
                        <div id="currentSignPreview" class="hidden mb-3 p-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl">
                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-2">Tanda Tangan Saat Ini</p>
                            <div id="currentSignLoading" class="w-24 h-12 border border-gray-200 rounded bg-white dark:bg-gray-800 flex items-center justify-center hidden">
                                <svg class="animate-spin h-5 w-5 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </div>
                            <img id="currentSignImg" src="" alt="Current Signature" class="max-w-[140px] max-h-[70px] border border-gray-200 rounded bg-white p-1 hidden">
                        </div>

                        <input type="file" id="editSignImage" name="tanda_tangan" accept="image/png,image/jpeg" class="block w-full text-sm text-gray-500 border border-gray-200 rounded-xl cursor-pointer bg-gray-50 dark:text-gray-400 dark:bg-gray-900 dark:border-gray-700 focus:outline-none file:cursor-pointer file:bg-primary-50 dark:file:bg-primary-900/30 file:border-0 file:border-r file:border-solid file:border-gray-200 dark:file:border-gray-700 file:!mr-4 file:!py-3 file:!px-5 dark:file:text-primary-400 file:!text-primary-700 file:!font-bold hover:file:bg-primary-100 dark:hover:file:bg-primary-900/50 transition-all" />
                        <p class="text-[11px] text-gray-400 mt-1.5">Format PNG/JPG, maks 2MB. Tanda tangan akan muncul pada kartu digital.</p>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-900 flex items-center justify-end gap-3 rounded-b-2xl">
                    <button type="button" onclick="closeEditMemberModal()" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-200 rounded-xl transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 text-sm font-bold bg-primary-600 hover:bg-primary-700 text-white rounded-xl shadow-lg shadow-primary-600/20 transition-all flex items-center gap-2 cursor-pointer">
                        <span class="btn-text">Simpan Perubahan</span>
                        <svg class="btn-spinner animate-spin h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ===== Edit Member Modal =====
        let currentMemberData = null;

        function openEditMemberModal() {
            if (!currentMemberData) return;

            // Set form action
            document.getElementById('editMemberForm').action = currentMemberData.update_url;
            document.getElementById('editModalSubtitle').textContent = currentMemberData.name + ' — ' + currentMemberData.badge;
            
            // Set current role
            document.getElementById('editRole').value = currentMemberData.member_role_id;
            
            // Set role button text
            const rData = roleData[currentMemberData.member_role_id];
            if (rData) {
                document.getElementById('editRoleBtnText').innerText = rData.name;
            } else {
                document.getElementById('editRoleBtnText').innerText = 'Select Role';
            }
            
            document.getElementById('editSignImage').value = '';
            
            // Show current signature if exists
            const currentSignImg = document.getElementById('currentSignImg');
            const currentSignLoading = document.getElementById('currentSignLoading');
            const currentSignPreview = document.getElementById('currentSignPreview');
            
            currentSignImg.classList.add('hidden');
            
            if (currentMemberData.sign_image) {
                currentSignLoading.classList.remove('hidden');
                currentSignPreview.classList.remove('hidden');
                currentSignImg.onload = function() {
                    currentSignLoading.classList.add('hidden');
                    currentSignImg.classList.remove('hidden');
                };
                currentSignImg.src = '/storage/' + currentMemberData.sign_image;
            } else {
                currentSignPreview.classList.add('hidden');
            }
            
            updateRoleHint();

            // Show modal with animation
            const modal = document.getElementById('editMemberModal');
            const content = document.getElementById('editModalContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeEditMemberModal() {
            const modal = document.getElementById('editMemberModal');
            const content = document.getElementById('editModalContent');
            content.classList.add('scale-95', 'opacity-0');
            content.classList.remove('scale-100', 'opacity-100');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }

        const roleData = {
            @foreach($memberRoles as $role)
                '{{ $role->id }}': { isSign: {{ $role->penandatangan ? 'true' : 'false' }}, isSingle: {{ $role->tunggal ? 'true' : 'false' }}, name: '{{ addslashes($role->nama) }}' },
            @endforeach
        };

        window.selectEditRole = function(id, name) {
            selectDropdownOption('editRole', 'editRoleBtnText', 'editRoleDropdownMenu', id, name);
            updateRoleHint();
        };

        function updateRoleHint() {
            const val = document.getElementById('editRole').value;
            const data = roleData[val];
            
            if (!data) return;
            
            const isSign = data.isSign;
            const isSingle = data.isSingle;
            const hint = document.getElementById('editRoleHint');
            
            let hintText = '';
            if (isSign && isSingle) {
                hintText = 'Jabatan ini hanya bisa dipegang oleh 1 orang. Tanda tangan akan muncul di kartu digital.';
            } else if (isSingle) {
                hintText = 'Jabatan ini hanya bisa dipegang oleh 1 orang.';
            } else if (isSign) {
                hintText = 'Tanda tangan jabatan akan muncul di kartu digital.';
            } else {
                hintText = 'Jabatan anggota SPSI.';
            }
            hint.textContent = hintText;
        }

        // Update hint when role changes
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('editRole');
            if (roleSelect) {
                roleSelect.addEventListener('change', updateRoleHint);
            }
        });

        // Close modals on Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const editModal = document.getElementById('editMemberModal');
                if (editModal && !editModal.classList.contains('hidden')) {
                    closeEditMemberModal();
                }
                closeFilterModal();
            }
        });
    </script>
</x-app-layout>
