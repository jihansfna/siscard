@props(['checked' => false])

<input type="checkbox" @if($checked) checked @endif {{ $attributes->merge(['class' => 'w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 focus:ring-2 dark:border-gray-600 dark:focus:ring-primary-600 dark:ring-offset-gray-800 transition-colors cursor-pointer']) }}>
