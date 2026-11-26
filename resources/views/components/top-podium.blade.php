@props(['podium'])

@php
    $first = $podium->get(0);
    $second = $podium->get(1);
    $third = $podium->get(2);

    $slots = [
        ['entry' => $second, 'rank' => 2, 'medal' => '🥈', 'ring' => 'border-slate-300', 'ptsColor' => 'text-slate-500', 'pedestal' => 'bg-slate-100 border-slate-200 text-slate-400 h-20 sm:h-24', 'avatarSize' => 'lg'],
        ['entry' => $first, 'rank' => 1, 'medal' => '🥇', 'ring' => 'border-amber-500', 'ptsColor' => 'text-amber-600', 'pedestal' => 'bg-amber-50 border-amber-200 text-amber-500 h-28 sm:h-32', 'avatarSize' => 'xl'],
        ['entry' => $third, 'rank' => 3, 'medal' => '🥉', 'ring' => 'border-rose-400', 'ptsColor' => 'text-rose-600', 'pedestal' => 'bg-rose-50 border-rose-200 text-rose-400 h-16 sm:h-20', 'avatarSize' => 'lg'],
    ];
@endphp

<div class="grid grid-cols-3 items-end gap-3 sm:gap-6 max-w-2xl mx-auto pt-4">
    @foreach ($slots as $slot)
        @if ($slot['entry'])
            @php
                $member = $slot['entry']['member'];
                $score = $slot['entry']['score'];
            @endphp
            <a href="{{ route('members.show', $member) }}"
               class="group flex flex-col items-center text-center transition-transform duration-200 hover:-translate-y-1.5">
                <div class="text-xl sm:text-2xl mb-1.5 animate-pulse-soft">{{ $slot['medal'] }}</div>
                <div class="p-1 rounded-full border-4 {{ $slot['ring'] }} bg-white shadow-sm group-hover:shadow-md transition-shadow">
                    <x-member-avatar :member="$member" :size="$slot['avatarSize']" />
                </div>
                <div class="mt-2.5 text-xs sm:text-sm font-extrabold text-slate-900 group-hover:text-[#163CA8] transition-colors line-clamp-1">
                    {{ $member->short_name }}
                </div>
                <div class="text-[11px] text-slate-400 line-clamp-1">{{ $member->division->name }}</div>
                <div class="mt-0.5 text-xs sm:text-sm font-extrabold {{ $slot['ptsColor'] }}">
                    {{ $score }} pts
                </div>
                <div class="w-full mt-3 rounded-t-2xl border border-b-0 flex items-center justify-center font-black text-xl sm:text-2xl {{ $slot['pedestal'] }}">
                    #{{ $slot['rank'] }}
                </div>
            </a>
        @else
            <div></div>
        @endif
    @endforeach
</div>
