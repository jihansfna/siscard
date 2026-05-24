<x-app-layout>
    <x-slot name="title">Members</x-slot>

    <section class="bg-white border border-blue-100 rounded-2xl p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <h2 class="text-xl font-bold text-gray-800">Members Management</h2>

            <div class="flex flex-wrap items-center gap-3">
                <form class="flex items-center gap-2" method="GET" action="{{ route('dashboard.members') }}">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                        </div>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search member..." class="pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50">
                    </div>

                    <div class="relative hidden sm:block">
                        <select name="status" onchange="this.form.submit()" class="pl-3 pr-8 py-2 border border-gray-200 rounded-lg text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50">
                            <option value="All Status" {{ request('status') == 'All Status' ? 'selected' : '' }}>All Status</option>
                            <option value="Registered Member" {{ request('status') == 'Registered Member' ? 'selected' : '' }}>Registered Member</option>
                            <option value="Pending Verification" {{ request('status') == 'Pending Verification' ? 'selected' : '' }}>Pending Verification</option>
                            <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-gray-400">
                            <x-heroicon-o-chevron-down class="w-4 h-4" />
                        </div>
                    </div>
                </form>

                {{-- Sort Dropdown --}}
                <div class="relative" id="sortDropdownContainer">
                    <button type="button" onclick="toggleDropdown('sortDropdown')" class="flex items-center gap-2 px-4 py-2 border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold transition-all">
                        <x-heroicon-o-arrows-up-down class="w-4 h-4" />
                        <span>Sort</span>
                        <x-heroicon-o-chevron-down class="w-3 h-3 text-gray-400" />
                    </button>
                    <div id="sortDropdown" class="absolute right-0 mt-2 w-40 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-50 hidden transition-all">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'desc']) }}" class="flex items-center justify-between px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <span>Data Terbaru</span>
                            @if(request('sort', 'desc') === 'desc')
                                <x-heroicon-m-check class="w-4 h-4 text-primary-600" />
                            @endif
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'asc']) }}" class="flex items-center justify-between px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <span>Data Terlama</span>
                            @if(request('sort') === 'asc')
                                <x-heroicon-m-check class="w-4 h-4 text-primary-600" />
                            @endif
                        </a>
                    </div>
                </div>

                {{-- Export Dropdown --}}
                <div class="relative" id="exportDropdownContainer">
                    <button type="button" onclick="toggleDropdown('exportDropdown')" class="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-emerald-600/20 active:scale-95">
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                        <span>Export</span>
                        <x-heroicon-o-chevron-down class="w-3 h-3" />
                    </button>
                    <div id="exportDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-50 hidden transition-all">
                        <a href="{{ route('dashboard.export.members.excel') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM9.5 11.5l2 3.5-2 3.5h1.5l1.25-2.5L13.5 18.5H15l-2-3.5 2-3.5h-1.5l-1.25 2.5-1.25-2.5H9.5z"/></svg>
                            <span>Export Excel</span>
                        </a>
                        <a href="{{ route('dashboard.export.members.pdf') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM10.5 11c-.83 0-1.5.67-1.5 1.5v4c0 .83.67 1.5 1.5 1.5h3c.83 0 1.5-.67 1.5-1.5v-4c0-.83-.67-1.5-1.5-1.5h-3z"/></svg>
                            <span>Export PDF</span>
                        </a>
                    </div>
                </div>

                <button type="button" onclick="openAddMemberModal()" class="flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm shadow-primary-600/30">
                    <span>Add Members</span>
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

        <div class="border border-gray-200 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-gray-700 text-xs uppercase font-semibold border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 w-10">
                                <input type="checkbox" id="selectAllMembers" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" onclick="toggleSelectAllMembers(this)">
                            </th>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Employee</th>
                            <th class="px-4 py-3">Position</th>
                            <th class="px-4 py-3">Department</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($members as $index => $member)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3">
                                    <input type="checkbox" name="ids[]" value="{{ $member->id }}" form="bulkDeleteForm" class="member-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500" onclick="updateBulkDeleteBar()">
                                </td>
                                <td class="px-4 py-3">{{ $members->firstItem() + $index }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $member->employee->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $member->employee->badge }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $member->employee->position ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $member->employee->department ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $member->role->name ?? 'Member' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($member->status == 'registered')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Registered</span>
                                    @elseif($member->status == 'pending')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($member->status) }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $memberData = [
                                            'id' => $member->id,
                                            'name' => $member->employee->name,
                                            'badge' => $member->employee->badge,
                                            'status' => $member->status,
                                            'created_at' => $member->created_at->format('d F Y, H:i'),
                                            'added_by' => 'Admin',
                                            'image' => $member->employee->image,
                                            'birth_place' => $member->employee->birth_place ?? '-',
                                            'birth_date' => $member->employee->birth_date?->format('d F Y') ?? '-',
                                            'join_date' => $member->employee->join_date?->format('d F Y') ?? '-',
                                            'address' => $member->employee->address ?? '-',
                                            'role' => $member->role->name ?? '-',
                                            'member_role_id' => $member->member_role_id,
                                            'sign_image' => $member->sign_image,
                                            'verify_token' => \App\Http\Controllers\CardController::encryptToken($member->uuid),
                                             'qr_base64' => 'data:image/svg+xml;base64,' . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->margin(1)->errorCorrection('H')->generate(url('/verify/' . \App\Http\Controllers\CardController::encryptToken($member->uuid)))),
                                            'card_download_url' => route('dashboard.members.card.download', $member->id),
                                            'update_url' => route('dashboard.members.update', $member->id),
                                            'logs' => $member->logs->map(function($log) {
                                                return [
                                                    'activity' => $log->activity,
                                                    'description' => $log->description,
                                                    'actor_name' => $log->actor ? $log->actor->name : 'System',
                                                    'actor_badge' => $log->actor ? $log->actor->badge : '',
                                                    'created_at_date' => $log->created_at->format('l'),
                                                    'created_at_time' => $log->created_at->format('d F Y, H.i'),
                                                ];
                                            })->toArray(),
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
                                        <p>Belum ada data member yang ditambahkan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($members->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $members->links() }}
            </div>
            @endif
        </div>
    </section>

    <!-- Add Member Modal -->
    <div id="addMemberModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-gray-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
                <h3 class="text-lg font-bold text-gray-800">Add New Members</h3>
                <button type="button" onclick="closeAddMemberModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
            <form action="{{ route('dashboard.members.store') }}" method="POST" id="addMemberForm" class="flex flex-col overflow-hidden form-with-loading">
                @csrf
                <div class="p-6 overflow-y-auto">
                    <div class="mb-4">
                        <p class="text-sm text-gray-600">Pilih employee di bawah ini untuk ditambahkan sebagai Member. Hanya employee yang belum menjadi member dan masih aktif yang ditampilkan.</p>
                    </div>
                    
                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-700 text-xs uppercase font-semibold border-b border-gray-200 sticky top-0">
                                <tr>
                                    <th class="px-4 py-3 w-10">
                                        <input type="checkbox" id="selectAllEmployees" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" onclick="toggleSelectAll(this)">
                                    </th>
                                    <th class="px-4 py-3">No</th>
                                    <th class="px-4 py-3">Employee</th>
                                    <th class="px-4 py-3">Position</th>
                                    <th class="px-4 py-3">Department</th>
                                    <th class="px-4 py-3">Line Code</th>
                                    <th class="px-4 py-3">End Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($availableEmployees as $index => $emp)
                                    <tr class="hover:bg-gray-50/50 transition-colors available-employee-row">
                                        <td class="px-4 py-3">
                                            <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}" class="employee-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500" onclick="updateSelectAllHeaderState()">
                                        </td>
                                        <td class="px-4 py-3">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-900">{{ $emp->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $emp->badge }}</div>
                                        </td>
                                        <td class="px-4 py-3">{{ $emp->position ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $emp->department ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $emp->line ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            @if($emp->end_date)
                                                <span class="text-xs text-gray-600">{{ $emp->end_date->format('d M Y') }}</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Active</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                            <p>Semua employee sudah terdaftar sebagai member atau sudah tidak aktif.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <!-- Client-side pagination for available employees in modal -->
                        <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-t border-gray-100 hidden" id="availableEmployeesPagination">
                            <span class="text-xs text-gray-500 font-medium" id="availPageInfo">Showing 1 to 5 of 9 employees</span>
                            <div class="flex gap-2">
                                <button type="button" onclick="changeAvailPage(-1)" id="availPrevBtn" class="px-3 py-1.5 text-xs font-bold bg-white border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all">Previous</button>
                                <button type="button" onclick="changeAvailPage(1)" id="availNextBtn" class="px-3 py-1.5 text-xs font-bold bg-white border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all">Next</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3 flex-shrink-0">
                    <button type="button" onclick="closeAddMemberModal()" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-200 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 text-sm font-bold bg-primary-600 hover:bg-primary-700 text-white rounded-xl shadow-lg shadow-primary-600/20 transition-all flex items-center gap-2">
                        <span class="btn-text">Tambahkan Terpilih</span>
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
            document.getElementById('availPageInfo').textContent = `Showing ${startIdx} to ${endIdx} of ${totalRows} employees`;
            
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
            document.getElementById('detailAddedInfo').textContent = 'Added by ' + member.added_by + ', ' + member.created_at;
            
            const statusColors = {
                'registered': 'bg-green-100 text-green-800',
                'pending': 'bg-yellow-100 text-yellow-800',
                'inactive': 'bg-gray-100 text-gray-800',
                'rejected': 'bg-red-100 text-red-800'
            };
            const statusLabels = {
                'registered': 'Registered',
                'pending': 'Waiting Confirmation',
                'inactive': 'Inactive',
                'rejected': 'Rejected'
            };
            const sColor = statusColors[member.status] || 'bg-gray-100 text-gray-800';
            const sLabel = statusLabels[member.status] || member.status;
            
            document.getElementById('detailStatusBadgeTop').className = 'inline-flex items-center px-2 py-1 rounded text-xs font-semibold ' + sColor;
            document.getElementById('detailStatusBadgeTop').textContent = sLabel;
            document.getElementById('detailStatusBadge').className = 'inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold ' + sColor;
            document.getElementById('detailStatusBadge').textContent = sLabel;
            
            // Profile image
            if (member.image) {
                document.getElementById('detailImage').src = '/storage/' + member.image;
                document.getElementById('detailImage').classList.remove('hidden');
                document.getElementById('detailImageFallback').classList.add('hidden');
            } else {
                document.getElementById('detailImage').classList.add('hidden');
                document.getElementById('detailImageFallback').classList.remove('hidden');
                document.getElementById('detailImageFallback').textContent = member.name.charAt(0).toUpperCase();
            }
            
            // QR Code - encrypted token URL (loaded instantly from pre-generated base64)
            document.getElementById('detailQrCode').src = member.qr_base64;
            
            document.getElementById('detailBirth').textContent = member.birth_place + ', ' + member.birth_date;
            document.getElementById('detailJoinDate').textContent = member.join_date;
            document.getElementById('detailRole').textContent = member.role;
            document.getElementById('detailAddress').textContent = member.address;
            
            // Signature image display
            if (member.sign_image) {
                document.getElementById('detailSign').classList.add('hidden');
                document.getElementById('detailSignImage').src = '/storage/' + member.sign_image;
                document.getElementById('detailSignImage').classList.remove('hidden');
            } else {
                document.getElementById('detailSign').textContent = '-';
                document.getElementById('detailSign').classList.remove('hidden');
                document.getElementById('detailSignImage').classList.add('hidden');
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
                    msgText.textContent = 'Menunggu konfirmasi admin. Kartu digital belum dapat dicetak.';
                    msgContainer.className = 'mb-4 p-4 border rounded-xl text-sm font-semibold flex items-center gap-3 bg-blue-50 border-blue-200 text-blue-800';
                } else if (member.status === 'inactive') {
                    msgText.textContent = 'Member sudah tidak aktif. Akses kartu digital telah dicabut.';
                    msgContainer.className = 'mb-4 p-4 border rounded-xl text-sm font-semibold flex items-center gap-3 bg-red-50 border-red-200 text-red-800';
                } else if (member.status === 'rejected') {
                    msgText.textContent = 'Pendaftaran ditolak. Kartu digital tidak tersedia.';
                    msgContainer.className = 'mb-4 p-4 border rounded-xl text-sm font-semibold flex items-center gap-3 bg-red-50 border-red-200 text-red-800';
                } else {
                    msgText.textContent = 'Kartu digital belum dapat di-generate. Status keanggotaan harus "Registered".';
                    msgContainer.className = 'mb-4 p-4 border rounded-xl text-sm font-semibold flex items-center gap-3 bg-amber-50 border-amber-200 text-amber-800';
                }
            }
            
            // Card front preview - Photo
            if (member.image) {
                document.getElementById('cardPhotoImg').src = '/storage/' + member.image;
                document.getElementById('cardPhotoImg').classList.remove('hidden');
                document.getElementById('cardPhotoFallback').classList.add('hidden');
            } else {
                document.getElementById('cardPhotoImg').classList.add('hidden');
                document.getElementById('cardPhotoFallback').classList.remove('hidden');
                document.getElementById('cardPhotoFallback').textContent = member.name.charAt(0).toUpperCase();
            }
            
            // Card front QR (loaded instantly from pre-generated base64)
            document.getElementById('cardFrontQr').src = member.qr_base64;
            
            // Card data
            document.getElementById('cardBadge').textContent = member.badge;
            document.getElementById('cardName').textContent = member.name;
            document.getElementById('cardBirth').textContent = member.birth_place + ' / ' + member.birth_date;
            document.getElementById('cardAddress').textContent = member.address;

            // Ensure we open Info tab by default
            switchMemberTab('info');
            
            // Render History Timeline
            const timelineContainer = document.getElementById('detailHistoryTimeline');
            timelineContainer.innerHTML = '';
            
            if (member.logs && member.logs.length > 0) {
                // Backend is ascending. To show newest at the top, reverse the array.
                const sortedLogs = [...member.logs].reverse();
                
                sortedLogs.forEach(log => {
                    const item = document.createElement('div');
                    item.className = 'relative pl-8';
                    item.innerHTML = `
                        <!-- Red Dot -->
                        <div class="absolute w-4 h-4 bg-red-600 rounded-full -left-[9px] top-1 border-4 border-white shadow-sm"></div>
                        
                        <!-- Content -->
                        <div class="flex flex-col sm:flex-row sm:items-start gap-x-6 gap-y-2">
                            <!-- Date & Time -->
                            <div class="w-32 flex-shrink-0 pt-0.5">
                                <p class="text-sm font-bold text-gray-900">${log.created_at_date}</p>
                                <p class="text-xs font-medium text-gray-500">${log.created_at_time}</p>
                            </div>
                            
                            <!-- Main Detail -->
                            <div class="flex-1">
                                <h5 class="text-sm font-bold text-gray-800 mb-2">${log.description}</h5>
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-red-600 flex items-center justify-center text-[10px] text-white font-bold overflow-hidden">
                                        ${log.actor_name.charAt(0).toUpperCase()}
                                    </div>
                                    <span class="text-xs text-gray-600 font-medium">${log.actor_name} ${log.actor_badge ? '(' + log.actor_badge + ')' : ''}</span>
                                </div>
                            </div>
                        </div>
                    `;
                    timelineContainer.appendChild(item);
                });
            } else {
                timelineContainer.innerHTML = '<p class="text-sm text-gray-500 ml-4">No history records found.</p>';
            }

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

            const sortContainer = document.getElementById('sortDropdownContainer');
            if (sortContainer && !sortContainer.contains(e.target)) {
                document.getElementById('sortDropdown').classList.add('hidden');
            }
        });
    </script>

    <!-- Member Detail Off-Canvas Modal -->
    <div id="memberDetailDrawer" onclick="if(event.target === this) closeMemberDetail()" class="fixed inset-0 z-50 hidden bg-gray-900/50 backdrop-blur-sm transition-opacity">
        <div class="fixed inset-y-0 right-0 w-full max-w-2xl bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col" id="drawerContent">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white z-10 sticky top-0">
                <div>
                    <h3 class="text-xl font-bold text-gray-800" id="detailNameTop">NAME</h3>
                    <p class="text-sm text-gray-500" id="detailBadgeTop">BADGE</p>
                    <div class="mt-2">
                        <span id="detailStatusBadgeTop" class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold"></span>
                    </div>
                    <p class="text-xs text-gray-400 mt-2" id="detailAddedInfo">Added by ...</p>
                </div>
                <button type="button" onclick="closeMemberDetail()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors absolute top-4 right-4 cursor-pointer">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
            
            <!-- Tabs -->
            <div class="px-6 border-b border-gray-100 flex gap-6 bg-white sticky top-[120px] z-10">
                <button type="button" onclick="switchMemberTab('info')" id="tabInfoBtn" class="py-3 text-sm font-bold text-primary-600 border-b-2 border-primary-600">Information</button>
                <button type="button" onclick="switchMemberTab('history')" id="tabHistoryBtn" class="py-3 text-sm font-medium text-gray-500 hover:text-gray-700">History</button>
            </div>

            <!-- Body -->
            <div class="p-6 overflow-y-auto flex-1 bg-gray-50/50" id="tabInfoContent">
                <h4 class="text-base font-bold text-gray-800 mb-4">User Information</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Photo & Basic -->
                    <div class="flex flex-col items-center text-center">
                        <div class="w-32 h-40 bg-red-600 rounded-lg overflow-hidden shadow-md mb-4 border-2 border-white">
                            <img id="detailImage" src="" alt="Profile" class="w-full h-full object-cover hidden">
                            <div id="detailImageFallback" class="w-full h-full flex items-center justify-center text-white text-4xl font-bold"></div>
                        </div>
                        <h5 class="font-bold text-gray-900 text-lg" id="detailName">NAME</h5>
                        <p class="text-gray-600 font-medium" id="detailBadge">BADGE</p>
                    </div>
                    
                    <!-- QR Code -->
                    <div class="flex flex-col items-center justify-center">
                        <img id="detailQrCode" src="" alt="QR" class="w-32 h-32 mb-2 rounded border border-gray-200 shadow-sm p-1 bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8 mb-8">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Place and Date of Birth</p>
                        <p class="font-bold text-gray-900 text-sm" id="detailBirth">-</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Status</p>
                        <div><span id="detailStatusBadge" class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold"></span></div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Join Date</p>
                        <p class="font-bold text-gray-900 text-sm" id="detailJoinDate">-</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Role</p>
                        <p class="font-bold text-gray-900 text-sm" id="detailRole">-</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">PUK</p>
                        <p class="font-bold text-gray-900 text-sm" id="detailPuk">PT. Sat Nusapersada Tbk</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Address</p>
                        <p class="font-bold text-gray-900 text-sm" id="detailAddress">-</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Image of Signature</p>
                        <div id="detailSignContainer">
                            <p class="font-bold text-gray-900 text-sm" id="detailSign">-</p>
                            <img id="detailSignImage" src="" alt="Signature" class="hidden mt-1 max-w-[120px] max-h-[60px] border border-gray-200 rounded bg-white p-1">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <hr class="border-gray-200 mb-6">
                <div class="flex items-center gap-3 mb-8">
                    <button type="button" onclick="openEditMemberModal()" class="flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold transition-all shadow-lg shadow-primary-600/20 active:scale-95 cursor-pointer">
                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                        <span>Edit Member</span>
                    </button>
                </div>

                <!-- Card Preview + Download -->
                <hr class="border-gray-200 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-base font-bold text-gray-800">Card Preview</h4>
                    <a id="downloadCardBtn" href="#" class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-sm font-bold transition-all shadow-lg shadow-emerald-600/20 active:scale-95 no-underline">
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                        <span>Unduh Kartu Digital</span>
                    </a>
                </div>
                
                <div id="cardNotRegisteredMsg" class="hidden mb-4 p-4 border rounded-xl text-sm font-semibold flex items-center gap-3 bg-amber-50 border-amber-200 text-amber-800">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5 flex-shrink-0" />
                    <span id="cardNotRegisteredMsgText">Kartu digital belum dapat di-generate. Status keanggotaan harus "Registered".</span>
                </div>

                <div id="cardPreviewContainer" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Card Front Preview -->
                    <div class="border border-gray-200 rounded-xl bg-white shadow-sm overflow-hidden p-4 relative h-52 flex items-center justify-center">
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
                                <div class="flex-shrink-0">
                                    <div class="w-14 h-[70px] bg-red-600 rounded-sm overflow-hidden" id="cardFrontPhoto">
                                        <img id="cardPhotoImg" src="" class="w-full h-full object-cover hidden" alt="" style="image-rendering: auto;">
                                        <div id="cardPhotoFallback" class="w-14 h-[70px] bg-red-600 flex items-center justify-center text-white font-bold text-lg"></div>
                                    </div>
                                </div>
                                <div class="flex-1 flex flex-col items-center justify-center">
                                    <img id="cardFrontQr" src="" alt="QR" class="w-14 h-14 border border-gray-200 bg-white p-0.5 rounded" style="image-rendering: auto;">
                                </div>
                                <div class="flex-shrink-0 flex flex-col items-center">
                                    <img src="{{ asset('logo_lem_spsi.jpg') }}" class="w-14 h-14 rounded-full object-contain" alt="Logo LEM SPSI" style="image-rendering: auto;">
                                    <p class="text-[6px] text-center text-gray-500 mt-0.5 font-bold">SP LEM - SPSI</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Back Preview -->
                    <div class="border border-gray-200 rounded-xl bg-white shadow-sm p-4 relative h-52 flex flex-col justify-between">
                        <div class="flex justify-between items-center mb-1.5 border-b border-gray-100 pb-1">
                            <img src="{{ asset('logo_lem_spsi.jpg') }}" class="w-6 h-6 rounded-full object-contain" alt="Logo LEM">
                            <div class="text-center">
                                <p class="text-[8px] font-bold">PIMPINAN UNIT KERJA</p>
                                <p class="text-[8px] font-bold">SP LEM SPSI</p>
                                <p class="text-[9px] font-bold">PT.SATNUSA PERSADA TBK</p>
                            </div>
                            <img src="{{ asset('logo_kspsi.png') }}" class="w-6 h-6 rounded-full object-contain" alt="Logo KSPSI">
                        </div>
                        <div class="text-[8px] space-y-0.5 flex-1">
                            <div class="flex"><div class="w-14 font-semibold">Nama</div><div>: <span id="cardName"></span></div></div>
                            <div class="flex"><div class="w-14 font-semibold">Tempat/Tgl</div><div>: <span id="cardBirth"></span></div></div>
                            <div class="flex"><div class="w-14 font-semibold">P.U.K</div><div>: PT. Satnusa Persada Tbk</div></div>
                            <div class="flex"><div class="w-14 font-semibold">Alamat</div><div class="flex-1 truncate">: <span id="cardAddress"></span></div></div>
                        </div>
                        <div class="flex justify-between mt-1 pt-1 border-t border-gray-100">
                            <div class="text-[8px] text-center">
                                <p class="font-semibold">Ketua</p>
                                <div id="cardKetuaSign" class="h-6 flex items-center justify-center">
                                    @if(isset($ketua) && $ketua->sign_image)
                                        <img id="cardKetuaSignImg" src="{{ asset('storage/' . $ketua->sign_image) }}" class="max-h-5" alt="Tanda Tangan Ketua">
                                    @else
                                        <img id="cardKetuaSignImg" src="" class="max-h-5 hidden" alt="">
                                    @endif
                                </div>
                                <p class="font-bold text-gray-800" id="cardKetuaName">{{ isset($ketua) ? $ketua->employee->name : '' }}</p>
                            </div>
                            <div class="text-[8px] text-center">
                                <p class="font-semibold">Sekretaris</p>
                                <div id="cardSekretarisSign" class="h-6 flex items-center justify-center">
                                    @if(isset($sekretaris) && $sekretaris->sign_image)
                                        <img id="cardSekretarisSignImg" src="{{ asset('storage/' . $sekretaris->sign_image) }}" class="max-h-5" alt="Tanda Tangan Sekretaris">
                                    @else
                                        <img id="cardSekretarisSignImg" src="" class="max-h-5 hidden" alt="">
                                    @endif
                                </div>
                                <p class="font-bold text-gray-800" id="cardSekretarisName">{{ isset($sekretaris) ? $sekretaris->employee->name : '' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between text-xs text-gray-500 mt-2 px-2">
                    <span>Front View</span>
                    <span>Back View</span>
                </div>
            </div>

            <!-- History Tab Content -->
            <div class="p-6 overflow-y-auto flex-1 bg-gray-50/50 hidden" id="tabHistoryContent">
                <div class="relative border-l-2 border-red-500 ml-4 py-2 space-y-8" id="detailHistoryTimeline">
                    <!-- Logs will be injected here via JS -->
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Member Popup Modal -->
    <div id="editMemberModal" onclick="if(event.target === this) closeEditMemberModal()" class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-gray-900/60 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform scale-95 opacity-0 transition-all duration-200" id="editModalContent">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Edit Member</h3>
                    <p class="text-xs text-gray-500 mt-0.5" id="editModalSubtitle">-</p>
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
                        <label for="editRole" class="block text-sm font-bold text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
                        <select id="editRole" name="member_role_id" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                            @foreach($memberRoles as $role)
                                <option value="{{ $role->id }}" 
                                    data-is-sign="{{ $role->is_sign ? '1' : '0' }}"
                                    data-is-single="{{ $role->is_single ? '1' : '0' }}">
                                    {{ $role->name }}
                                    @if($role->is_sign) — Tanda tangan muncul di kartu @endif
                                    @if($role->is_single) (Maks. 1 orang) @endif
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-gray-400 mt-1.5" id="editRoleHint">Pilih role untuk member ini.</p>
                    </div>

                    <!-- Signature Upload -->
                    <div id="signatureUploadSection">
                        <label for="editSignImage" class="block text-sm font-bold text-gray-700 mb-2">Upload Tanda Tangan</label>
                        
                        <!-- Current Signature Preview -->
                        <div id="currentSignPreview" class="hidden mb-3 p-3 bg-gray-50 border border-gray-200 rounded-xl">
                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-2">Tanda Tangan Saat Ini</p>
                            <img id="currentSignImg" src="" alt="Current Signature" class="max-w-[140px] max-h-[70px] border border-gray-200 rounded bg-white p-1">
                        </div>

                        <input type="file" id="editSignImage" name="sign_image" accept="image/png,image/jpeg" class="block w-full text-xs text-gray-500
                            file:mr-3 file:py-2.5 file:px-4
                            file:rounded-xl file:border-0
                            file:text-xs file:font-bold
                            file:bg-primary-50 file:text-primary-800
                            hover:file:bg-primary-100
                            border border-gray-200 bg-gray-50 rounded-xl cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all" />
                        <p class="text-[11px] text-gray-400 mt-1.5">Format PNG/JPG, maksimal 2MB. Tanda tangan akan muncul pada kartu digital.</p>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/80 flex items-center justify-end gap-3">
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
            document.getElementById('editSignImage').value = '';
            
            // Show current signature if exists
            if (currentMemberData.sign_image) {
                document.getElementById('currentSignImg').src = '/storage/' + currentMemberData.sign_image;
                document.getElementById('currentSignPreview').classList.remove('hidden');
            } else {
                document.getElementById('currentSignPreview').classList.add('hidden');
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

        function updateRoleHint() {
            const select = document.getElementById('editRole');
            
            // If the current member's role isn't in the options (e.g. 'Member' role was filtered out),
            // fallback to the first available option to prevent JS crash.
            if (select.selectedIndex === -1 && select.options.length > 0) {
                select.selectedIndex = 0;
            }
            
            const selected = select.options[select.selectedIndex];
            if (!selected) return;
            
            const isSign = selected.dataset.isSign === '1';
            const isSingle = selected.dataset.isSingle === '1';
            const hint = document.getElementById('editRoleHint');
            
            let hintText = '';
            if (isSign && isSingle) {
                hintText = '⚠️ Role ini hanya bisa dipegang 1 orang. Tanda tangan akan muncul di kartu digital.';
            } else if (isSingle) {
                hintText = '⚠️ Role ini hanya bisa dipegang 1 orang.';
            } else if (isSign) {
                hintText = 'Tanda tangan role ini akan muncul di kartu digital.';
            } else {
                hintText = 'Role standar anggota SPSI.';
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

        // Close edit modal on Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const editModal = document.getElementById('editMemberModal');
                if (editModal && !editModal.classList.contains('hidden')) {
                    closeEditMemberModal();
                }
            }
        });
    </script>
</x-app-layout>
