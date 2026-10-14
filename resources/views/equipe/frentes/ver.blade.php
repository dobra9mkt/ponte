@php
    $fases = $frente->etapas;
    $prontas = $fases->filter(fn ($fase) => $fase->estado->finalizada())->count();
    $editandoServico = $errors->hasAny(['nome', 'nome_cliente', 'inicio_em', 'prazo_final']) && old('_form') === 'servico';
    $novaFase = old('_fase') === 'nova';
@endphp
<x-layouts.equipe :titulo="$frente->nome" :voltar="['url' => route('equipe.clientes.show', $cliente), 'nome' => $cliente->empresa]">
    <div class="mx-auto max-w-4xl">
        <section x-data="alternar" @if ($editandoServico) x-init="abrir" @endif class="rounded-cartao bg-white p-5 shadow-cartao sm:p-6">
            <div x-show="!aberto" class="flex flex-wrap items-center justify-between gap-4">
                <dl class="flex flex-wrap gap-x-8 gap-y-2 text-sm">
                    <div>
                        <dt class="text-mudo">{{ __('equipe.inicio') }}</dt>
                        <dd class="font-medium text-navy">{{ $frente->inicio_em?->format('d/m/Y') ?? __('equipe.a_combinar') }}</dd>
                    </div>
                    <div>
                        <dt class="text-mudo">{{ __('equipe.termino_previsto') }}</dt>
                        <dd class="font-medium text-navy">{{ $frente->prazo_final?->format('d/m/Y') ?? __('equipe.a_combinar') }}</dd>
                    </div>
                    <div>
                        <dt class="text-mudo">{{ __('equipe.fases') }}</dt>
                        <dd class="font-medium text-navy">{{ $prontas }}/{{ $fases->count() }}</dd>
                    </div>
                    <div>
                        <dt class="text-mudo">{{ __('equipe.portal') }}</dt>
                        <dd><x-etiqueta :tom="$frente->publicada_em ? 'sucesso' : 'neutro'">{{ __($frente->publicada_em ? 'equipe.visivel_cliente' : 'equipe.so_equipe') }}</x-etiqueta></dd>
                    </div>
                </dl>
                <x-botao variante="secundario" type="button" class="min-h-10 py-1.5 text-sm" x-on:click="abrir"><x-icone nome="editar" class="h-4 w-4" />{{ __('equipe.editar_servico') }}</x-botao>
            </div>
            <x-progresso x-show="!aberto" :feitas="$prontas" :total="$fases->count()" class="mt-5" />

            <form method="POST" action="{{ route('equipe.frentes.update', [$cliente, $frente]) }}" x-show="aberto" x-cloak class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="_form" value="servico">
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-campo nome="nome" :rotulo="__('equipe.nome_servico')" :valor="$frente->nome" :isolado="! $editandoServico" required maxlength="100" />
                    <x-campo nome="nome_cliente" :rotulo="__('equipe.nome_cliente')" :valor="$frente->nome_cliente" :isolado="! $editandoServico" maxlength="100" opcional />
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-campo nome="inicio_em" tipo="date" :rotulo="__('equipe.inicio')" :valor="$frente->inicio_em?->toDateString()" :isolado="! $editandoServico" />
                    <x-campo nome="prazo_final" tipo="date" :rotulo="__('equipe.termino')" :valor="$frente->prazo_final?->toDateString()" :isolado="! $editandoServico" />
                </div>
                <div class="flex flex-wrap gap-2">
                    <x-botao>{{ __('equipe.salvar') }}</x-botao>
                    <x-botao variante="discreto" type="button" x-on:click="fechar">{{ __('equipe.cancelar') }}</x-botao>
                </div>
            </form>
            <div x-show="aberto" x-cloak class="mt-5 flex flex-wrap items-start gap-x-6 gap-y-2 border-t border-linha pt-4">
                <form method="POST" action="{{ route('equipe.frentes.visibilidade', [$cliente, $frente]) }}">
                    @csrf
                    <input type="hidden" name="mostrar" value="{{ $frente->publicada_em ? 0 : 1 }}">
                    <button class="inline-flex min-h-11 items-center gap-2 text-sm font-medium text-tec underline-offset-4 hover:underline">
                        <x-icone :nome="$frente->publicada_em ? 'olho-fechado' : 'olho'" class="h-4 w-4" />{{ __($frente->publicada_em ? 'equipe.ocultar_do_cliente' : 'equipe.mostrar_ao_cliente') }}
                    </button>
                </form>
                @can('delete', $frente)
                    <x-excluir :acao="route('equipe.frentes.destroy', [$cliente, $frente])"
                        :rotulo="__('equipe.excluir_servico')" :mensagem="__('equipe.excluir_servico_confirmar', ['servico' => $frente->nome])" />
                @endcan
            </div>
        </section>

        <section class="mt-8" aria-labelledby="fases-titulo">
            <h2 id="fases-titulo" class="sr-only">{{ __('equipe.fases') }}</h2>
            <div class="space-y-4">
                @foreach ($fases as $fase)
                    <x-fase :etapa="$fase" :numero="$loop->iteration" :primeira="$loop->first" :ultima="$loop->last" />
                @endforeach
            </div>

            <div x-data="alternar" @if ($novaFase || $fases->isEmpty()) x-init="abrir" @endif class="mt-4">
                <button type="button" x-on:click="abrir" x-show="!aberto" class="flex min-h-14 w-full items-center justify-center gap-2 rounded-cartao border border-dashed border-tec/40 font-titulo text-sm font-semibold text-tec transition hover:border-tec hover:bg-white">
                    <x-icone nome="mais" class="h-5 w-5" />{{ __('equipe.nova_fase') }}
                </button>
                <form method="POST" action="{{ route('equipe.etapas.store', [$cliente, $frente]) }}" x-show="aberto" x-cloak class="space-y-4 rounded-cartao bg-white p-5 shadow-cartao sm:p-6">
                    @csrf
                    <input type="hidden" name="_fase" value="nova">
                    <h3 class="text-base font-semibold">{{ __('equipe.nova_fase') }}</h3>
                    <x-campos-fase prefixo="nova-" :isolado="! $novaFase" />
                    <div class="flex flex-wrap gap-2">
                        <x-botao>{{ __('equipe.adicionar_fase') }}</x-botao>
                        @if ($fases->isNotEmpty())
                            <x-botao variante="discreto" type="button" x-on:click="fechar">{{ __('equipe.cancelar') }}</x-botao>
                        @endif
                    </div>
                </form>
            </div>
        </section>
    </div>
</x-layouts.equipe>
