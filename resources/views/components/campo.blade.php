@props([
    'nome',
    'rotulo',
    'tipo' => 'text',
    'valor' => null,
    'ajuda' => null,
    'opcional' => false,
    'linhas' => null,
    'prefixo' => '',
    'isolado' => false,
])
@php
    $id = 'campo-'.$prefixo.str_replace(['[', ']', '.'], '-', $nome);
    $erro = $isolado ? null : $errors->first($nome);
    $atual = $isolado ? $valor : old($nome, $valor);
    $descritores = trim(($ajuda ? $id.'-ajuda ' : '').($erro ? $id.'-erro' : ''));
    $senha = $tipo === 'password';
@endphp
<div class="space-y-1.5">
    <label for="{{ $id }}" class="block text-[0.95rem] font-medium text-navy">
        {{ $rotulo }}
        @if ($opcional)<span class="font-normal text-mudo">(opcional)</span>@endif
    </label>
    @if ($linhas)
        <textarea id="{{ $id }}" name="{{ $nome }}" rows="{{ $linhas }}" @if ($descritores) aria-describedby="{{ $descritores }}" @endif @if ($erro) aria-invalid="true" @endif {{ $attributes->class(['campo', 'campo-erro' => $erro]) }}>{{ $atual }}</textarea>
    @elseif ($senha)
        <div class="relative" x-data="senhaVisivel">
            <input id="{{ $id }}" name="{{ $nome }}" type="password" x-bind:type="tipo" @if ($descritores) aria-describedby="{{ $descritores }}" @endif @if ($erro) aria-invalid="true" @endif {{ $attributes->class(['campo pr-24', 'campo-erro' => $erro]) }}>
            <button type="button" class="absolute inset-y-0 right-0 flex min-w-20 items-center justify-center rounded-r-campo text-sm font-medium text-tec hover:bg-azul-claro" x-on:click="alternar" x-text="rotulo" aria-controls="{{ $id }}">Mostrar</button>
        </div>
    @else
        <input id="{{ $id }}" name="{{ $nome }}" type="{{ $tipo }}" value="{{ $tipo === 'file' ? '' : $atual }}" @if ($descritores) aria-describedby="{{ $descritores }}" @endif @if ($erro) aria-invalid="true" @endif {{ $attributes->class(['campo', 'campo-erro' => $erro, 'file:mr-3 file:rounded-lg file:border-0 file:bg-azul-claro file:px-3 file:py-1.5 file:font-medium file:text-tec' => $tipo === 'file']) }}>
    @endif
    @if ($ajuda)
        <p id="{{ $id }}-ajuda" class="text-sm text-mudo">{{ $ajuda }}</p>
    @endif
    @if ($erro)
        <p id="{{ $id }}-erro" class="text-sm font-medium text-alerta">{{ $erro }}</p>
    @endif
</div>
