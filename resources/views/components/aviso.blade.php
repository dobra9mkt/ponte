@props(['tipo' => 'info'])
@php
    $estilo = match ($tipo) {
        'sucesso' => 'border-teal/30 bg-teal/10 text-teal',
        'erro' => 'border-alerta/30 bg-alerta/10 text-alerta',
        'atencao' => 'border-ouro/40 bg-ouro-claro/15 text-ouro',
        default => 'border-tec/20 bg-azul-claro text-tec',
    };
    $icone = match ($tipo) {
        'sucesso' => '✓',
        'erro', 'atencao' => '!',
        default => 'i',
    };
@endphp
<div {{ $attributes->class(['flex gap-3 rounded-campo border px-4 py-3 text-[0.95rem]', $estilo]) }} role="{{ $tipo === 'erro' ? 'alert' : 'status' }}">
    <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full border border-current text-xs font-bold" aria-hidden="true">{{ $icone }}</span>
    <div class="min-w-0 text-texto">{{ $slot }}</div>
</div>
