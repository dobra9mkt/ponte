@props(['nome', 'rotulo', 'opcoes', 'valor' => null, 'ajuda' => null, 'prefixo' => '', 'isolado' => false])
@php
    $id = 'campo-'.$prefixo.$nome;
    $erro = $isolado ? null : $errors->first($nome);
    $atual = (string) ($isolado ? $valor : old($nome, $valor));
@endphp
<div class="space-y-1.5">
    <label for="{{ $id }}" class="block text-[0.95rem] font-medium text-navy">{{ $rotulo }}</label>
    <select id="{{ $id }}" name="{{ $nome }}" @if ($ajuda) aria-describedby="{{ $id }}-ajuda" @endif @if ($erro) aria-invalid="true" @endif {{ $attributes->class(['campo', 'campo-erro' => $erro]) }}>
        @foreach ($opcoes as $chave => $texto)
            <option value="{{ $chave }}" @selected($atual === (string) $chave)>{{ $texto }}</option>
        @endforeach
    </select>
    @if ($ajuda)
        <p id="{{ $id }}-ajuda" class="text-sm text-mudo">{{ $ajuda }}</p>
    @endif
    @if ($erro)
        <p class="text-sm font-medium text-alerta">{{ $erro }}</p>
    @endif
</div>
