@props(['size' => 'md'])

@php
    $sizeClasses = match ($size) {
        'sm' => 'w-6 h-6',
        'lg' => 'w-11 h-11',
        default => 'w-9 h-9',
    };
@endphp

<svg {{ $attributes->merge(['class' => $sizeClasses]) }} viewBox="0 0 48 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <!-- Left top chevron (Red) -->
    <path d="M18.6 4.4L6.8 14.2C5.7 15.1 5.7 16.9 6.8 17.8L10.6 20.9L22.4 11.1C23.5 10.2 23.5 8.4 22.4 7.5L18.6 4.4Z" fill="#EA4335"/>
    <!-- Left bottom chevron (Blue) -->
    <path d="M18.6 27.6L6.8 17.8C5.7 16.9 5.7 15.1 6.8 14.2L10.6 11.1L22.4 20.9C23.5 21.8 23.5 23.6 22.4 24.5L18.6 27.6Z" fill="#4285F4"/>
    <!-- Right top chevron (Green) -->
    <path d="M29.4 4.4L41.2 14.2C42.3 15.1 42.3 16.9 41.2 17.8L37.4 20.9L25.6 11.1C24.5 10.2 24.5 8.4 25.6 7.5L29.4 4.4Z" fill="#34A853"/>
    <!-- Right bottom chevron (Yellow) -->
    <path d="M29.4 27.6L41.2 17.8C42.3 16.9 42.3 15.1 41.2 14.2L37.4 11.1L25.6 20.9C24.5 21.8 24.5 23.6 25.6 24.5L29.4 27.6Z" fill="#FBBC04"/>
</svg>
