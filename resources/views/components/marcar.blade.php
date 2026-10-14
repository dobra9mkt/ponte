@props(['nome', 'rotulo', 'marcado' => false, 'ajuda' => null, 'prefixo' => '', 'isolado' => false])
@php
    $id = 'campo-'.$prefixo.$nome;
    $atual = $isolado ? $marcado : (bool) old($nome, $marcado);
@endphp
<div class="flex gap-3">
    <input type="hidden" name="{{ $nome }}" value="0">
    <input id="{{ $id }}" type="checkbox" name="{{ $nome }}" value="1" @checked($atual) {{ $attributes->class('mt-1 h-5 w-5 shrink-0 rounded border-linha text-azul focus:ring-azul/25') }}>
    <label for="{{ $id }}" class="text-[0.95rem] leading-snug">
        <span class="font-medium text-navy">{{ $rotulo }}</span>
        @if ($ajuda)
            <span class="block text-sm text-mudo">{{ $ajuda }}</span>
        @endif
    </label>
</div>
