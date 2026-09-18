@props([
    'title' => null,
    'description' => null,
])

<x-public-layout :title="$title ?? ('Login & Registration - ' . config('app.name', 'RGPL Academy'))" :description="$description">
    <div class="py-12 sm:py-16 bg-gradient-to-b from-slate-50 via-slate-50 to-slate-100 min-h-[calc(100vh-22rem)] flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8">
        <div class="w-full sm:max-w-lg bg-white shadow-xl shadow-slate-200/60 rounded-2xl border border-slate-200/80 p-6 sm:p-10 transition-all">
            <div class="flex justify-center mb-6">
                <x-rgpl-logo class="scale-110" />
            </div>

            {{ $slot }}
        </div>
    </div>
</x-public-layout>
