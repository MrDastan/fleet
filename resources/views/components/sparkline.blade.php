@php
    $w = 64; $h = 26;
    $max = max($points); $min = min($points);
    $rng = ($max - $min) ?: 1;
    $step = $w / (count($points) - 1);
    $d = '';
    foreach ($points as $i => $p) {
        $x = round($i * $step, 1);
        $y = round($h - 2 - (($p - $min) / $rng) * ($h - 6), 1);
        $d .= ($i === 0 ? "M{$x} {$y}" : " L{$x} {$y}");
    }
    $lastX = $w;
    $lastY = round($h - 2 - ((end($points) - $min) / $rng) * ($h - 6), 1);
@endphp
<svg width="{{ $w }}" height="{{ $h }}" viewBox="0 0 {{ $w }} {{ $h }}" fill="none" style="overflow:visible">
    <path d="{{ $d }}" stroke="{{ $color }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
    <circle cx="{{ $lastX }}" cy="{{ $lastY }}" r="2.6" fill="{{ $color }}" />
</svg>
