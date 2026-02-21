@props(['title', 'value', 'subtitle' => '', 'icon' => '📊', 'color' => 'primary', 'link' => null])

<div class="stat-card {{ $color }}" @if($link) onclick="window.location.href='{{ $link }}'" style="cursor: pointer;" @endif>
    <div class="stat-card-header">
        <span class="stat-card-title">{{ $title }}</span>
        <div class="stat-card-icon">{{ $icon }}</div>
    </div>
    <div class="stat-card-value">{{ $value }}</div>
    @if($subtitle)
    <div class="stat-card-subtitle">{{ $subtitle }}</div>
    @endif
</div>