@props([
    'showTagline' => true,
    'class' => 'h-10 w-auto',
    'inverted' => false,
])

<div {{ $attributes->merge(['class' => 'flex items-center gap-3 select-none']) }}>
    <!-- Vector Emblem -->
    <div class="relative shrink-0 flex items-center justify-center">
        <svg class="h-10 w-10 drop-shadow-sm" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="shield_g_{{ $attributes->get('id', 'default') }}" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#1E3A8A"/>
                    <stop offset="100%" stop-color="#2563EB"/>
                </linearGradient>
                <linearGradient id="flame_g_{{ $attributes->get('id', 'default') }}" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#F59E0B"/>
                    <stop offset="100%" stop-color="#EA580C"/>
                </linearGradient>
            </defs>
            <path d="M 32 3 Q 56 7 61 17 Q 61 45 32 62 Q 3 45 3 17 Q 8 7 32 3 Z" fill="url(#shield_g_{{ $attributes->get('id', 'default') }})" />
            <path d="M 32 6 Q 52 10 57 18 Q 57 41 32 57 Q 7 41 7 18 Q 12 10 32 6 Z" stroke="#FFFFFF" stroke-opacity="0.25" stroke-width="1.5" fill="none"/>
            <path d="M 32 29 Q 20 24 12 27 L 12 43 Q 20 40 32 46 Q 44 40 52 43 L 52 27 Q 44 24 32 29 Z" fill="#FFFFFF"/>
            <path d="M 32 29 L 32 46" stroke="#1E3A8A" stroke-width="2"/>
            <path d="M 32 11 C 34 17 39 18 39 22 C 39 26 36 28 32 28 C 28 28 25 26 25 22 C 25 18 30 17 32 11 Z" fill="url(#flame_g_{{ $attributes->get('id', 'default') }})" />
            <circle cx="49" cy="46" r="8" fill="#10B981" />
            <path d="M 45.5 46 L 48 48.5 L 53 43.5" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>

    <!-- Typography -->
    <div class="flex flex-col">
        <div class="flex items-baseline font-black tracking-tight text-xl leading-none {{ $inverted ? 'text-white' : 'text-slate-900' }}" style="color: {{ $inverted ? '#FFFFFF' : '#0F172A' }};">
            <span style="color: {{ $inverted ? '#FFFFFF' : '#0F172A' }};">RGPL</span>&nbsp;<span class="{{ $inverted ? 'text-amber-400' : 'text-amber-600' }}" style="color: {{ $inverted ? '#FBBF24' : '#D97706' }};">Academy</span>
        </div>
        @if($showTagline)
            <span class="text-[10px] font-semibold tracking-wider {{ $inverted ? 'text-slate-300' : 'text-slate-500' }} uppercase mt-0.5" style="color: {{ $inverted ? '#CBD5E1' : '#64748B' }};">Your Exam Preparation Partner</span>
        @endif
    </div>
</div>
