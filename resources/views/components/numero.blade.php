@props(['rotulo', 'valor', 'icone', 'tom' => 'neutro'])
@php
    $cor = match ($tom) {
        'atencao' => 'bg-ouro-claro/20 text-ouro',
        'alerta' => 'bg-alerta/10 text-alerta',
        default => 'bg-azul-claro text-tec',
    };
@endphp
<div class="flex items-center gap-4 rounded-cartao bg-white p-5 shadow-cartao">
    <span @class(['flex h-11 w-11 items-center justify-center rounded-campo', $cor])>
        <x-icone :nome="$icone" class="h-5 w-5" />
    </span>
    <div>
        <p class="font-titulo text-2xl font-bold leading-none text-navy">{{ $valor }}</p>
        <p class="mt-1 text-sm text-mudo">{{ $rotulo }}</p>
    </div>
</div>
