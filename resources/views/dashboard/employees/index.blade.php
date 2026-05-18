<x-app-layout>
    <x-slot name="title">Master Employees</x-slot>

    <section class="bg-white border border-blue-100 rounded-2xl p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <h2 class="text-xl font-bold text-gray-800">Master Data Employees</h2>

            <div class="flex flex-wrap items-center gap-3">
                <form class="flex items-center gap-2" method="GET" action="{{ route('dashboard.employees.index') }}">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                        </div>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / badge..." class="pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50">
                    </div>
                </form>

                <button type="button" onclick="document.getElementById('addEmployeeModal').classList.remove('hidden'); document.getElementById('addEmployeeModal').classList.add('flex')" class="flex items-center gap-3 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-primary-600/20 active:scale-95">
                    <span>Add Employee</span>
                    <x-heroicon-o-user-plus class="w-4 h-4" />
                </button>
            </div>
        </div>

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

        <form id="bulkDeleteForm" action="{{ route('dashboard.employees.bulk_destroy') }}" method="POST" class="hidden">
            @csrf
        </form>

        <div class="border border-gray-200 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-gray-700 text-xs uppercase font-semibold border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 w-10">
                                <input type="checkbox" id="selectAllEmployees" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" onclick="toggleSelectAllEmployees(this)">
                            </th>
                            <th class="px-4 py-3">Badge</th>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Department</th>
                            <th class="px-4 py-3">Position</th>
                            <th class="px-4 py-3">Join Date</th>
                            <th class="px-4 py-3">End Date</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($employees as $employee)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3">
                                    <input type="checkbox" name="ids[]" value="{{ $employee->id }}" form="bulkDeleteForm" class="employee-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500" onclick="updateBulkDeleteBar()">
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $employee->badge }}</td>
                                <td class="px-4 py-3">{{ $employee->name }}</td>
                                <td class="px-4 py-3">{{ $employee->department ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $employee->position ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $employee->join_date?->format('d M Y') ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if($employee->end_date)
                                        @if($employee->end_date->lt(now()->startOfDay()))
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">{{ $employee->end_date->format('d M Y') }}</span>
                                        @else
                                            <span>{{ $employee->end_date->format('d M Y') }}</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right space-x-1">
                                    <!-- Edit Employee Button -->
                                    <button type="button" onclick="document.getElementById('editEmployeeModal-{{ $employee->id }}').classList.remove('hidden'); document.getElementById('editEmployeeModal-{{ $employee->id }}').classList.add('flex')" class="inline-flex p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit Employee">
                                        <x-heroicon-o-pencil-square class="w-5 h-5" />
                                    </button>

                                    <!-- Edit Employee Modal -->
                                    <div id="editEmployeeModal-{{ $employee->id }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/50 backdrop-blur-sm text-left">
                                        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden max-h-[90vh] flex flex-col">
                                            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
                                                <h3 class="text-lg font-bold text-gray-800">Edit Employee: {{ $employee->name }}</h3>
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
                                                            <label class="block text-sm font-bold text-gray-700">Badge ID <span class="text-red-500">*</span></label>
                                                            <input name="badge" type="text" value="{{ old('badge', $employee->badge) }}" required 
                                                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm">
                                                        </div>

                                                        <div class="space-y-2">
                                                            <label class="block text-sm font-bold text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                                                            <input name="name" type="text" value="{{ old('name', $employee->name) }}" required 
                                                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm">
                                                        </div>

                                                        <div class="space-y-2">
                                                            <label class="block text-sm font-bold text-gray-700">Department</label>
                                                            <input name="department" type="text" value="{{ old('department', $employee->department) }}" 
                                                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm">
                                                        </div>

                                                        <div class="space-y-2">
                                                            <label class="block text-sm font-bold text-gray-700">Position</label>
                                                            <input name="position" type="text" value="{{ old('position', $employee->position) }}" 
                                                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm">
                                                        </div>

                                                        <div class="space-y-2">
                                                            <label class="block text-sm font-bold text-gray-700">Line</label>
                                                            <input name="line" type="text" value="{{ old('line', $employee->line) }}" 
                                                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm">
                                                        </div>

                                                        <div class="space-y-2">
                                                            <label class="block text-sm font-bold text-gray-700">Join Date</label>
                                                            <input name="join_date" type="date" value="{{ old('join_date', $employee->join_date?->format('Y-m-d')) }}" 
                                                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm">
                                                        </div>

                                                        <div class="space-y-2">
                                                            <label class="block text-sm font-bold text-gray-700">End Date</label>
                                                            <input name="end_date" type="date" value="{{ old('end_date', $employee->end_date?->format('Y-m-d')) }}" 
                                                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm">
                                                            <p class="text-xs text-gray-500 mt-1">Kosongkan jika employee masih aktif</p>
                                                        </div>

                                                        <div class="space-y-2">
                                                            <label class="block text-sm font-bold text-gray-700">Tempat Lahir</label>
                                                            <input name="birth_place" type="text" value="{{ old('birth_place', $employee->birth_place) }}" 
                                                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm">
                                                        </div>

                                                        <div class="space-y-2">
                                                            <label class="block text-sm font-bold text-gray-700">Tanggal Lahir</label>
                                                            <input name="birth_date" type="date" value="{{ old('birth_date', $employee->birth_date?->format('Y-m-d')) }}" 
                                                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm">
                                                        </div>

                                                        @if($employee->image)
                                                            <div class="space-y-2 md:col-span-2">
                                                                <label class="block text-sm font-bold text-gray-700">Foto Employee</label>
                                                                <div class="mb-2">
                                                                    <img src="{{ asset('storage/' . $employee->image) }}" alt="Foto" class="w-20 h-20 object-cover rounded-lg border border-gray-200">
                                                                </div>
                                                                <input name="image" type="file" accept=".jpg,.jpeg,.png" onchange="handleFileChange(this)"
                                                                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm">
                                                                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, JPEG. Max: 2MB</p>
                                                            </div>
                                                        @else
                                                            <div class="space-y-2">
                                                                <label class="block text-sm font-bold text-gray-700">Foto Employee</label>
                                                                <input name="image" type="file" accept=".jpg,.jpeg,.png" onchange="handleFileChange(this)"
                                                                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm">
                                                                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, JPEG. Max: 2MB</p>
                                                            </div>
                                                        @endif

                                                        <div class="space-y-2 md:col-span-2">
                                                            <label class="block text-sm font-bold text-gray-700">Alamat</label>
                                                            <textarea name="address" rows="3" placeholder="Alamat lengkap..." 
                                                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm resize-none">{{ old('address', $employee->address) }}</textarea>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3 flex-shrink-0">
                                                <button type="button" onclick="document.getElementById('editEmployeeModal-{{ $employee->id }}').classList.add('hidden'); document.getElementById('editEmployeeModal-{{ $employee->id }}').classList.remove('flex')" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-200 rounded-lg transition-colors">
                                                    Batal
                                                </button>
                                                <button type="submit" form="editEmployeeForm-{{ $employee->id }}" class="px-6 py-2.5 text-sm font-bold bg-primary-600 hover:bg-primary-700 text-white rounded-xl shadow-lg shadow-primary-600/20 transition-all flex items-center gap-2">
                                                    <span class="btn-text">Update</span>
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
                                        <p>Belum ada data employee.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($employees->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $employees->links() }}
            </div>
            @endif
        </div>
    </section>

    <!-- Add Employee Modal -->
    <div id="addEmployeeModal" class="fixed inset-0 z-50 {{ $errors->any() ? 'flex' : 'hidden' }} items-center justify-center bg-gray-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
                <h3 class="text-lg font-bold text-gray-800">Add New Employee</h3>
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
                            <label for="badge" class="block text-sm font-bold text-gray-700">Badge ID <span class="text-red-500">*</span></label>
                            <input id="badge" name="badge" type="text" value="{{ old('badge') }}" placeholder="Contoh: 12345" required 
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                        </div>

                        <div class="space-y-2 group">
                            <label for="name" class="block text-sm font-bold text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Nama Employee" required 
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                        </div>

                        <div class="space-y-2 group">
                            <label for="department" class="block text-sm font-bold text-gray-700">Department</label>
                            <input id="department" name="department" type="text" value="{{ old('department') }}" placeholder="Contoh: IT" 
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                        </div>

                        <div class="space-y-2 group">
                            <label for="position" class="block text-sm font-bold text-gray-700">Position</label>
                            <input id="position" name="position" type="text" value="{{ old('position') }}" placeholder="Contoh: Staff" 
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                        </div>

                        <div class="space-y-2 group">
                            <label for="line" class="block text-sm font-bold text-gray-700">Line</label>
                            <input id="line" name="line" type="text" value="{{ old('line') }}" placeholder="Contoh: Line 1" 
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                        </div>

                        <div class="space-y-2 group">
                            <label for="join_date" class="block text-sm font-bold text-gray-700">Join Date</label>
                            <input id="join_date" name="join_date" type="date" value="{{ old('join_date') }}" 
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                        </div>

                        <div class="space-y-2 group">
                            <label for="end_date" class="block text-sm font-bold text-gray-700">End Date</label>
                            <input id="end_date" name="end_date" type="date" value="{{ old('end_date') }}" 
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                            <p class="text-xs text-gray-500 mt-1">Kosongkan jika employee masih aktif</p>
                        </div>

                        <div class="space-y-2 group">
                            <label for="birth_place" class="block text-sm font-bold text-gray-700">Tempat Lahir</label>
                            <input id="birth_place" name="birth_place" type="text" value="{{ old('birth_place') }}" placeholder="Contoh: Batam" 
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                        </div>

                        <div class="space-y-2 group">
                            <label for="birth_date" class="block text-sm font-bold text-gray-700">Tanggal Lahir</label>
                            <input id="birth_date" name="birth_date" type="date" value="{{ old('birth_date') }}" 
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                        </div>

                        <div class="space-y-2 group">
                            <label for="image" class="block text-sm font-bold text-gray-700">Foto Employee</label>
                            <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png" onchange="handleFileChange(this)"
                                class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, JPEG. Max: 2MB</p>
                        </div>

                        <div class="space-y-2 group md:col-span-2">
                            <label for="address" class="block text-sm font-bold text-gray-700">Alamat</label>
                            <textarea id="address" name="address" rows="3" placeholder="Alamat lengkap..." 
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm resize-none">{{ old('address') }}</textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3 flex-shrink-0">
                <button type="button" onclick="document.getElementById('addEmployeeModal').classList.add('hidden'); document.getElementById('addEmployeeModal').classList.remove('flex')" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-200 rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit" form="addEmployeeForm" class="px-6 py-2.5 text-sm font-bold bg-primary-600 hover:bg-primary-700 text-white rounded-xl shadow-lg shadow-primary-600/20 transition-all flex items-center gap-2">
                    <span class="btn-text">Simpan Employee</span>
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
                        window.showToast('Format Salah!', 'Hanya format JPG, PNG, dan JPEG yang diperbolehkan.', 'error');
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
                        window.showToast('File Terlalu Besar!', 'Ukuran foto maksimal adalah 2MB.', 'error');
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
</x-app-layout>
