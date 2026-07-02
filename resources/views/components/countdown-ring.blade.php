@php
    $r = 17; $c = 2 * M_PI * $r;
    $pct = max(0.06, min(1, (60 - $days) / 60));
    $off = $c * (1 - $pct);
@endphp
<div class="ring-wrap">
    <svg width="44" height="44" viewBox="0 0 44 44">
        <circle cx="22" cy="22" r="{{ $r }}" fill="none" stroke="#EFEBE3" stroke-width="3.5" />
        <circle cx="22" cy="22" r="{{ $r }}" fill="none" stroke="{{ $color }}" stroke-width="3.5"
                stroke-linecap="round" stroke-dasharray="{{ $c }}" stroke-dashoffset="{{ $off }}" />
    </svg>
    <div class="ring-center">
        <span class="n" style="color:{{ $color }}">{{ $days }}</span>
        <span class="lbl">hari</span>
    </div>
</div>
