@props(['id', 'name', 'value' => '', 'placeholder' => 'Pilih tanggal', 'required' => false, 'class' => ''])

<div class="relative w-full">
    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
        <x-heroicon-o-calendar class="w-4 h-4 text-gray-500 dark:text-gray-400" />
    </div>
    <input 
        datepicker 
        datepicker-buttons 
        datepicker-autoselect-today
        datepicker-format="yyyy-mm-dd"
        type="text" 
        id="{{ $id }}"
        name="{{ $name }}"
        value="{{ $value }}"
        @if($required) required @endif
        class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-primary-500 focus:border-primary-500 block w-full ps-10 p-2.5 dark:bg-gray-900 dark:border-gray-700 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 transition-colors {{ $class }}" 
        placeholder="{{ $placeholder }}"
    >
</div>
