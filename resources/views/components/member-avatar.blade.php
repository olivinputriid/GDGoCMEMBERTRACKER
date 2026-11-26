@props([
    'member' => null,
    'name' => 'Member',
    'color' => '#163CA8',
    'avatarUrl' => null,
    'size' => 'md',
    'ring' => '',
])

@php
    $displayName = $member?->name ?? $name;
    $bgColor = $member?->avatar_color ?? $color;
    $photoUrl = $member?->avatar_url ?? $avatarUrl;
    $initials = $member?->initials ?? collect(explode(' ', trim($displayName)))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
    if ($initials === '') {
        $initials = 'GD';
    }

    $sizeClasses = match ($size) {
        'xs' => 'w-8 h-8 text-xs',
        'sm' => 'w-9 h-9 text-xs',
        'lg' => 'w-14 h-14 text-lg',
        'xl' => 'w-16 h-16 text-xl',
        '2xl' => 'w-20 h-20 text-2xl',
        default => 'w-10 h-10 text-sm',
    };
@endphp

@if ($photoUrl)
    <img src="{{ $photoUrl }}"
         alt="{{ $displayName }}"
         {{ $attributes->merge(['class' => "inline-block rounded-full object-cover shrink-0 shadow-sm bg-white {$sizeClasses} {$ring}"]) }}>
@else
    <div {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-full font-bold text-white shrink-0 select-none shadow-sm {$sizeClasses} {$ring}"]) }}
         style="background: linear-gradient(135deg, {{ $bgColor }}, {{ $bgColor }}dd);">
        {{ $initials }}
    </div>
@endif
