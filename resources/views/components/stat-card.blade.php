@props(['title', 'value', 'icon' => 'boxes', 'subtitle' => 'unit', 'caption' => '', 'stat' => '', 'emphasis' => false])

<article {{ $attributes->class(['metric-card', 'metric-featured' => $emphasis]) }}>
    <div class="metric-heading"><h2>{{ $title }}</h2><i class="bi bi-{{ $icon }}" aria-hidden="true"></i></div>
    <div class="metric-value"><strong @if($stat) data-stat="{{ $stat }}" @endif>{{ $value }}</strong><span>{{ $subtitle }}</span></div>
    @if($caption)<p class="metric-caption">{{ $caption }}</p>@endif
</article>
