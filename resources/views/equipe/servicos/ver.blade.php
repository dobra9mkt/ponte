@php
    $novaFase = old('_fase') === 'nova';
    $editandoNome = old('_form') === 'servico';
@endphp
<x-layouts.equipe :titulo="$modelo->nome" :subtitulo="__('equipe.vale_para_novos')" :voltar="['url' => route('equipe.servicos.index'), 'nome' => __('equipe.menu_servicos')]">
    <x-slot:acoes>
        <details class="relative" @if ($editandoNome) open @endif>
            <summary class="inline-flex min-h-11 cursor-pointer list-none items-center gap-2 rounded-campo border border-linha bg-white px-4 font-titulo text-[0.95rem] font-semibold text-navy transition hover:border-tec hover:text-tec [&::-webkit-details-marker]:hidden">
                <x-icone nome="editar" class="h-4 w-4" />{{ __('equipe.renomear') }}
            </summary>
            <form method="POST" action="{{ route('equipe.servicos.update', $modelo) }}" class="absolute right-0 z-20 mt-2 w-80 space-y-3 rounded-cartao bg-white p-4 shadow-xl ring-1 ring-linha">
                @csrf
                @method('PUT')
                <input type="hidden" name="_form" value="servico">
                <x-campo nome="nome" :rotulo="__('equipe.nome_servico')" :valor="$modelo->nome" :isolado="! $editandoNome" required maxlength="100" />
                <x-campo nome="nome_cliente" :rotulo="__('equipe.nome_cliente')" :valor="$modelo->nome_cliente" :isolado="! $editandoNome" maxlength="100" opcional />
                <x-botao largo>{{ __('equipe.salvar') }}</x-botao>
            </form>
        </details>
    </x-slot:acoes>

    <div class="mx-auto max-w-4xl space-y-3">
        @foreach ($modelo->etapas as $fase)
            @php($editando = (string) old('_fase') === (string) $fase->id)
            <article id="fase-{{ $fase->id }}" x-data="alternar" @if ($editando) x-init="abrir" @endif class="scroll-mt-24 rounded-cartao bg-white p-5 shadow-cartao">
                <div class="flex items-start gap-4">
                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-off text-xs font-bold text-mudo">{{ $loop->iteration }}</span>
                    <div class="min-w-0 flex-1">
                        <div x-show="!aberto">
                            <h3 class="break-words text-[1.05rem] font-semibold">{{ $fase->titulo }}</h3>
                            @if ($fase->responsavel || $fase->duracao_dias_uteis)
                                <p class="mt-1.5 flex flex-wrap gap-x-5 gap-y-1 text-sm text-mudo">
                                    @if ($fase->responsavel)<span class="inline-flex items-center gap-1.5"><x-icone nome="conta" class="h-4 w-4" />{{ $fase->responsavel }}</span>@endif
                                    @if ($fase->duracao_dias_uteis)<span class="inline-flex items-center gap-1.5"><x-icone nome="relogio" class="h-4 w-4" />{{ __('equipe.dias_uteis', ['n' => $fase->duracao_dias_uteis]) }}</span>@endif
                                </p>
                            @endif
                            @if ($fase->o_que_fazer)<p class="mt-2 whitespace-pre-line text-[0.95rem]">{{ $fase->o_que_fazer }}</p>@endif
                        </div>
                        <form method="POST" action="{{ route('equipe.servicos.fases.update', $fase) }}" x-show="aberto" x-cloak class="space-y-4">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="_fase" value="{{ $fase->id }}">
                            <x-campos-fase :fase="$fase" :prefixo="'padrao-'.$fase->id.'-'" :isolado="! $editando" padrao />
                            <div class="flex flex-wrap gap-2">
                                <x-botao>{{ __('equipe.salvar') }}</x-botao>
                                <x-botao variante="discreto" type="button" x-on:click="fechar">{{ __('equipe.cancelar') }}</x-botao>
                            </div>
                        </form>
                    </div>
                    <div class="flex shrink-0 items-center gap-0.5" x-show="!aberto">
                        @foreach (['subir' => ! $loop->first, 'descer' => ! $loop->last] as $direcao => $pode)
                            @if ($pode)
                                <form method="POST" action="{{ route('equipe.servicos.fases.mover', $fase) }}">
                                    @csrf
                                    <input type="hidden" name="direcao" value="{{ $direcao }}">
                                    <button class="flex min-h-10 min-w-10 items-center justify-center rounded-campo text-mudo transition hover:bg-off hover:text-tec" aria-label="{{ __('equipe.'.$direcao) }}" title="{{ __('equipe.'.$direcao) }}"><x-icone :nome="$direcao" class="h-4 w-4" /></button>
                                </form>
                            @endif
                        @endforeach
                        <button type="button" x-on:click="abrir" class="flex min-h-10 min-w-10 items-center justify-center rounded-campo text-mudo transition hover:bg-off hover:text-tec" aria-label="{{ __('equipe.editar') }}" title="{{ __('equipe.editar') }}"><x-icone nome="editar" class="h-4 w-4" /></button>
                        <form method="POST" action="{{ route('equipe.servicos.fases.destroy', $fase) }}">
                            @csrf
                            @method('DELETE')
                            <button class="flex min-h-10 min-w-10 items-center justify-center rounded-campo text-mudo transition hover:bg-alerta/5 hover:text-alerta" aria-label="{{ __('equipe.remover') }}" title="{{ __('equipe.remover') }}"><x-icone nome="lixo" class="h-4 w-4" /></button>
                        </form>
                    </div>
                </div>
            </article>
        @endforeach

        <div x-data="alternar" @if ($novaFase || $modelo->etapas->isEmpty()) x-init="abrir" @endif>
            <button type="button" x-on:click="abrir" x-show="!aberto" class="flex min-h-14 w-full items-center justify-center gap-2 rounded-cartao border border-dashed border-tec/40 font-titulo text-sm font-semibold text-tec transition hover:border-tec hover:bg-white">
                <x-icone nome="mais" class="h-5 w-5" />{{ __('equipe.nova_fase') }}
            </button>
            <form method="POST" action="{{ route('equipe.servicos.fases.store', $modelo) }}" x-show="aberto" x-cloak class="space-y-4 rounded-cartao bg-white p-5 shadow-cartao sm:p-6">
                @csrf
                <input type="hidden" name="_fase" value="nova">
                <h3 class="text-base font-semibold">{{ __('equipe.nova_fase') }}</h3>
                <x-campos-fase prefixo="nova-" :isolado="! $novaFase" padrao />
                <div class="flex flex-wrap gap-2">
                    <x-botao>{{ __('equipe.adicionar_fase') }}</x-botao>
                    @if ($modelo->etapas->isNotEmpty())
                        <x-botao variante="discreto" type="button" x-on:click="fechar">{{ __('equipe.cancelar') }}</x-botao>
                    @endif
                </div>
            </form>
        </div>

        <div x-data="confirmar" class="pt-6">
            <button type="button" x-on:click="abrir" x-show="!aberto" class="text-sm font-medium text-mudo underline-offset-4 hover:text-alerta hover:underline">{{ __('equipe.desativar_servico') }}</button>
            <form method="POST" action="{{ route('equipe.servicos.destroy', $modelo) }}" x-show="aberto" x-cloak class="max-w-xl rounded-cartao border border-alerta/30 bg-white p-5">
                @csrf
                @method('DELETE')
                <p class="text-sm">{{ __('equipe.desativar_confirmar') }}</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <x-botao variante="perigo">{{ __('equipe.desativar_servico') }}</x-botao>
                    <x-botao variante="secundario" type="button" x-on:click="fechar">{{ __('equipe.cancelar') }}</x-botao>
                </div>
            </form>
        </div>
    </div>
</x-layouts.equipe>
