@props(['tom' => 'neutro'])
@php
    $estilo = match ($tom) {
        'sucesso' => 'bg-teal/10 text-teal ring-teal/25',
        'info' => 'bg-azul-claro text-azul ring-azul/20',
        'atencao' => 'bg-ouro-claro/20 text-ouro ring-ouro/30',
        'alerta' => 'bg-alerta/10 text-alerta ring-alerta/25',
        default => 'bg-off text-mudo ring-linha',
    };
@endphp
<span {{ $attributes->class(['inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset', $estilo]) }}>
    <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
    {{ $slot }}
</span>
