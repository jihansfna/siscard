@props(['id', 'name', 'value' => '', 'placeholder' => 'Pilih tanggal'])

<input type="date" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}" 
    {{ $attributes->merge(['class' => 'w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent focus:bg-white dark:focus:bg-[#2A2A2A] transition-all cursor-pointer']) }}>
