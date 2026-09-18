@props([
    'showTagline' => true,
    'class' => 'h-10 w-auto',
    'inverted' => false,
])

<x-parikshamitra-logo :show-tagline="$showTagline" :inverted="$inverted" {{ $attributes->merge(['class' => $class]) }} />
