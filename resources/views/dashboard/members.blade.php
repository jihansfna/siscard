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

        <form id="bulkDeleteForm" action="{{ route('dashboard.members.bulk_destroy') }}" method="POST" class="hidden" onsubmit="return confirm('Apakah Anda yakin ingin menghapus member terpilih?');">
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
                                            'role' => $member->role->name ?? '-'
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
            
            if (member.image) {
                document.getElementById('detailImage').src = '/storage/' + member.image;
                document.getElementById('detailImage').classList.remove('hidden');
                document.getElementById('detailImageFallback').classList.add('hidden');
            } else {
                document.getElementById('detailImage').classList.add('hidden');
                document.getElementById('detailImageFallback').classList.remove('hidden');
                document.getElementById('detailImageFallback').textContent = member.name.charAt(0).toUpperCase();
            }
            
            document.getElementById('detailQrCode').src = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' + member.badge;
            
            document.getElementById('detailBirth').textContent = member.birth_place + ', ' + member.birth_date;
            document.getElementById('detailJoinDate').textContent = member.join_date;
            document.getElementById('detailRole').textContent = member.role;
            document.getElementById('detailAddress').textContent = member.address;
            
            document.getElementById('cardBadge').textContent = member.badge;
            document.getElementById('cardName').textContent = member.name;
            document.getElementById('cardBirth').textContent = member.birth_place + ' / ' + member.birth_date;
            document.getElementById('cardAddress').textContent = member.address;

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
    </script>

    <!-- Member Detail Off-Canvas Modal -->
    <div id="memberDetailDrawer" class="fixed inset-0 z-50 hidden bg-gray-900/50 backdrop-blur-sm transition-opacity">
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
                <button type="button" onclick="closeMemberDetail()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors absolute top-4 right-4">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
            
            <!-- Tabs -->
            <div class="px-6 border-b border-gray-100 flex gap-6 bg-white sticky top-[120px] z-10">
                <button class="py-3 text-sm font-bold text-primary-600 border-b-2 border-primary-600">Information</button>
                <button class="py-3 text-sm font-medium text-gray-500 hover:text-gray-700">History</button>
            </div>

            <!-- Body -->
            <div class="p-6 overflow-y-auto flex-1 bg-gray-50/50">
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
                        <p class="font-bold text-gray-900 text-sm" id="detailSign">-</p>
                    </div>
                </div>

                <hr class="border-gray-200 mb-6">
                <h4 class="text-base font-bold text-gray-800 mb-4">Card Preview</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Card Front Preview Mockup -->
                    <div class="border border-gray-200 rounded-xl bg-white shadow-sm overflow-hidden p-4 relative h-48 flex items-center justify-center">
                        <div class="relative z-10 w-full">
                            <div class="text-center mb-2">
                                <p class="text-[10px] font-bold text-gray-800">KARTU ANGGOTA</p>
                                <p class="text-xs font-bold text-primary-800">FSP LEM SPSI - KOTA BATAM</p>
                            </div>
                            <div class="flex gap-2">
                                <div class="w-16 h-20 bg-red-600 rounded-sm"></div>
                                <div class="flex-1">
                                    <p class="text-[8px] font-bold">NO. KTA: <span id="cardBadge" class="text-blue-600"></span></p>
                                    <div class="w-12 h-12 border border-gray-300 mt-1 bg-white"></div>
                                </div>
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex-shrink-0"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Card Back Preview Mockup -->
                    <div class="border border-gray-200 rounded-xl bg-white shadow-sm p-4 relative h-48 flex flex-col justify-between">
                        <div class="flex justify-between items-center mb-2 border-b border-gray-100 pb-1">
                            <div class="w-6 h-6 bg-blue-100 rounded-full"></div>
                            <div class="text-center">
                                <p class="text-[8px] font-bold">PIMPINAN UNIT KERJA</p>
                                <p class="text-[9px] font-bold">PT. SATNUSA PERSADA TBK</p>
                            </div>
                            <div class="w-6 h-6 bg-red-100 rounded-full"></div>
                        </div>
                        <div class="text-[8px] space-y-1">
                            <div class="flex"><div class="w-12">Nama</div><div>: <span id="cardName"></span></div></div>
                            <div class="flex"><div class="w-12">Tempat/Tgl</div><div>: <span id="cardBirth"></span></div></div>
                            <div class="flex"><div class="w-12">P.U.K</div><div>: PT. Satnusa Persada Tbk</div></div>
                            <div class="flex"><div class="w-12">Alamat</div><div class="flex-1 truncate">: <span id="cardAddress"></span></div></div>
                        </div>
                        <div class="flex justify-between mt-2 pt-2 border-t border-gray-100">
                            <div class="text-[8px] text-center"><p>Ketua</p><div class="h-6"></div><p>Miharso</p></div>
                            <div class="text-[8px] text-center"><p>Sekretaris</p><div class="h-6"></div><p>Rizo Marko</p></div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between text-xs text-gray-500 mt-2 px-2">
                    <span>Front View</span>
                    <span>Back View</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
