@props(['variante' => 'principal', 'href' => null, 'type' => 'submit', 'largo' => false])
@php
    $classes = [
        'inline-flex min-h-11 items-center justify-center gap-2 rounded-campo px-5 py-2.5 font-titulo text-[0.95rem] font-semibold transition disabled:cursor-not-allowed disabled:opacity-50',
        'w-full' => $largo,
        'bg-azul text-white hover:bg-azul-hover' => $variante === 'principal',
        'border border-linha bg-white text-navy hover:border-tec hover:text-tec' => $variante === 'secundario',
        'bg-alerta text-white hover:bg-alerta/90' => $variante === 'perigo',
        'text-tec hover:bg-azul-claro' => $variante === 'discreto',
    ];
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
