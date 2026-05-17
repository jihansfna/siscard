<x-app-layout>
    <x-slot name="title">Edit Master Employee</x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('dashboard.employees.index') }}" class="text-gray-400 hover:text-primary-600 transition-colors">
                <x-heroicon-o-arrow-left class="w-6 h-6" />
            </a>
            <h2 class="text-xl font-bold text-gray-800">Edit Employee</h2>
        </div>

        <section class="bg-white border border-blue-100 rounded-2xl p-6 shadow-sm">
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

            <form action="{{ route('dashboard.employees.update', $employee->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2 group">
                        <label for="badge" class="block text-sm font-bold text-gray-700">Badge ID <span class="text-red-500">*</span></label>
                        <input id="badge" name="badge" type="text" value="{{ old('badge', $employee->badge) }}" placeholder="Contoh: 12345" required 
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                    </div>

                    <div class="space-y-2 group">
                        <label for="name" class="block text-sm font-bold text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input id="name" name="name" type="text" value="{{ old('name', $employee->name) }}" placeholder="Nama Employee" required 
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                    </div>

                    <div class="space-y-2 group">
                        <label for="department" class="block text-sm font-bold text-gray-700">Department</label>
                        <input id="department" name="department" type="text" value="{{ old('department', $employee->department) }}" placeholder="Contoh: IT" 
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                    </div>

                    <div class="space-y-2 group">
                        <label for="position" class="block text-sm font-bold text-gray-700">Position</label>
                        <input id="position" name="position" type="text" value="{{ old('position', $employee->position) }}" placeholder="Contoh: Staff" 
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                    </div>

                    <div class="space-y-2 group">
                        <label for="line" class="block text-sm font-bold text-gray-700">Line</label>
                        <input id="line" name="line" type="text" value="{{ old('line', $employee->line) }}" placeholder="Contoh: Line 1" 
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                    </div>

                    <div class="space-y-2 group">
                        <label for="join_date" class="block text-sm font-bold text-gray-700">Join Date</label>
                        <input id="join_date" name="join_date" type="date" value="{{ old('join_date', $employee->join_date?->format('Y-m-d')) }}" 
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                    </div>

                    <div class="space-y-2 group">
                        <label for="birth_place" class="block text-sm font-bold text-gray-700">Birth Place</label>
                        <input id="birth_place" name="birth_place" type="text" value="{{ old('birth_place', $employee->birth_place) }}" placeholder="Tempat Lahir" 
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                    </div>

                    <div class="space-y-2 group">
                        <label for="birth_date" class="block text-sm font-bold text-gray-700">Birth Date</label>
                        <input id="birth_date" name="birth_date" type="date" value="{{ old('birth_date', $employee->birth_date?->format('Y-m-d')) }}" 
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">
                    </div>
                </div>

                <div class="space-y-2 group">
                    <label for="address" class="block text-sm font-bold text-gray-700">Address</label>
                    <textarea id="address" name="address" rows="3" placeholder="Alamat Lengkap" 
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white transition-all text-sm">{{ old('address', $employee->address) }}</textarea>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-primary-600/30 transition-all duration-300">
                        Update Employee
                    </button>
                </div>
            </form>
        </section>
    </div>
</x-app-layout>
