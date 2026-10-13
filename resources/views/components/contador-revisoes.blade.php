@props(['usadas' => 0, 'total' => 3])
<div {{ $attributes->class('flex items-center gap-3 rounded-campo bg-azul-claro px-4 py-3') }}>
    <span class="flex gap-1.5" aria-hidden="true">
        @for ($i = 1; $i <= $total; $i++)
            <span @class(['h-3.5 w-3.5 rounded-full border-2', 'border-azul bg-azul' => $i <= $usadas, 'border-tec/40 bg-white' => $i > $usadas])></span>
        @endfor
    </span>
    <span class="font-titulo text-[0.95rem] font-semibold text-navy">
        {{ $usadas === 0 ? __('ponte.revisoes.nenhuma') : __('ponte.revisoes.contador', ['usadas' => $usadas, 'total' => $total]) }}
    </span>
</div>
