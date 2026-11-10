@props(['tone' => 'neutral'])
<span {{ $attributes->class(['status-badge', 'status-'.$tone]) }}><span class="status-dot" aria-hidden="true"></span>{{ $slot }}</span>
