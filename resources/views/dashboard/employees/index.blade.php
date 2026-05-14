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

        @if(session('success'))
            <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-semibold flex items-center gap-3">
                <x-heroicon-s-check-circle class="w-5 h-5 flex-shrink-0" />
                {{ session('success') }}
            </div>
        @endif

        <div class="border border-gray-200 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-gray-700 text-xs uppercase font-semibold border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3">Badge</th>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Department</th>
                            <th class="px-4 py-3">Position</th>
                            <th class="px-4 py-3">Join Date</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($employees as $employee)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $employee->badge }}</td>
                                <td class="px-4 py-3">{{ $employee->name }}</td>
                                <td class="px-4 py-3">{{ $employee->department ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $employee->position ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('d M Y') : '-' }}</td>
                                <td class="px-4 py-3 text-right space-x-1">
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
                                                <form action="{{ route('dashboard.employees.update', $employee->id) }}" method="POST" class="space-y-6 form-with-loading" id="editEmployeeForm-{{ $employee->id }}">
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
                                                            <input name="join_date" type="date" value="{{ old('join_date', $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('Y-m-d') : '') }}" 
                                                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm">
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

                                    <form action="{{ route('dashboard.employees.destroy', $employee->id) }}" method="POST" class="inline-block form-with-loading" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data employee ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors flex items-center justify-center relative" title="Delete Employee">
                                            <x-heroicon-o-trash class="w-5 h-5 btn-icon" />
                                            <svg class="btn-spinner animate-spin h-5 w-5 text-red-600 hidden absolute" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
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

                <form action="{{ route('dashboard.employees.store') }}" method="POST" class="space-y-6 form-with-loading" id="addEmployeeForm">
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
</x-app-layout>
