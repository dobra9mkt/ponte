@props(['fase' => null, 'prefixo', 'isolado' => false, 'padrao' => false])
<div class="space-y-4">
    <x-campo nome="titulo" :rotulo="__('equipe.nome_fase')" :valor="$fase?->titulo" :prefixo="$prefixo" :isolado="$isolado" required maxlength="150" />
    <div @class(['grid gap-4', 'sm:grid-cols-3' => ! $padrao, 'sm:grid-cols-2' => $padrao])>
        <x-campo nome="responsavel" :rotulo="__('equipe.responsavel')" :valor="$fase?->responsavel" :prefixo="$prefixo" :isolado="$isolado" maxlength="100" />
        @if ($padrao)
            <x-campo nome="duracao_dias_uteis" tipo="number" :rotulo="__('equipe.duracao')" :valor="$fase?->duracao_dias_uteis" :prefixo="$prefixo" :isolado="$isolado" min="1" max="250" />
        @else
            <x-campo nome="entrada_em" tipo="date" :rotulo="__('equipe.entrada')" :valor="$fase?->entrada_em?->toDateString()" :prefixo="$prefixo" :isolado="$isolado" />
            <x-campo nome="termino_em" tipo="date" :rotulo="__('equipe.termino')" :valor="$fase?->termino_em?->toDateString()" :prefixo="$prefixo" :isolado="$isolado" />
        @endif
    </div>
    <x-campo nome="o_que_fazer" :rotulo="__('equipe.o_que_fazer')" :valor="$fase?->o_que_fazer" :prefixo="$prefixo" :isolado="$isolado" :linhas="2" maxlength="2000" />
</div>
