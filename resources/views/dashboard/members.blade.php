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

                <button type="button" onclick="document.getElementById('addMemberModal').classList.remove('hidden')" class="flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm shadow-primary-600/30">
                    <span>Add Members</span>
                    <x-heroicon-o-user-plus class="w-4 h-4" />
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-semibold flex items-center gap-3">
                <x-heroicon-s-check-circle class="w-5 h-5 flex-shrink-0" />
                {{ session('success') }}
            </div>
        @endif
        
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

        <div class="border border-gray-200 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-gray-700 text-xs uppercase font-semibold border-b border-gray-200">
                        <tr>
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
                                    <button class="inline-flex p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View Member Details">
                                        <x-heroicon-o-eye class="w-5 h-5" />
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
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
                <button type="button" onclick="document.getElementById('addMemberModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
            <form action="{{ route('dashboard.members.store') }}" method="POST" id="addMemberForm" class="flex flex-col overflow-hidden form-with-loading">
                @csrf
                <div class="p-6 overflow-y-auto">
                    <div class="mb-4">
                        <p class="text-sm text-gray-600">Pilih employee di bawah ini untuk ditambahkan sebagai Member. Hanya employee yang belum menjadi member yang ditampilkan.</p>
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
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($availableEmployees as $index => $emp)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-4 py-3">
                                            <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}" class="employee-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                        </td>
                                        <td class="px-4 py-3">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-900">{{ $emp->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $emp->badge }}</div>
                                        </td>
                                        <td class="px-4 py-3">{{ $emp->position ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $emp->department ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $emp->line ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                            <p>Semua employee sudah terdaftar sebagai member.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3 flex-shrink-0">
                    <button type="button" onclick="document.getElementById('addMemberModal').classList.add('hidden')" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-200 rounded-lg transition-colors">
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
        function toggleSelectAll(source) {
            checkboxes = document.querySelectorAll('.employee-checkbox');
            for(var i=0, n=checkboxes.length; i<n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }
    </script>
</x-app-layout>
