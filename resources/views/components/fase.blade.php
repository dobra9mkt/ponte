@props(['etapa', 'numero', 'primeira' => false, 'ultima' => false])
@php
    use App\Enums\EstadoEtapa;
    use App\Enums\SituacaoCard;
    $prefixo = 'fase-'.$etapa->id.'-';
    $editando = (string) old('_fase') === (string) $etapa->id;
    $feita = $etapa->estado === EstadoEtapa::Concluida;
    $emCurso = in_array($etapa->estado, [EstadoEtapa::EmProducao, EstadoEtapa::AguardandoCliente, EstadoEtapa::AjustesEmAndamento], true);
    $cards = $etapa->entregas;
    $total = $etapa->frente->revisoes_incluidas;
    $principal = $cards->first();
    $rascunho = $principal?->situacao === SituacaoCard::Rascunho ? $principal : null;
    $pedidoDoCliente = $rascunho ? $cards->get(1) : null;
    $anteriores = $cards->slice($pedidoDoCliente ? 2 : 1);
    $rascunhoFechado = $pedidoDoCliente && session('card_aberto') !== $rascunho->id && (string) old('_card') !== (string) $rascunho->id;
    $rotuloNovoCard = match (true) {
        $etapa->proximoEhVersaoFinal() => 'equipe.enviar_versao_final',
        $principal?->situacao === SituacaoCard::AjustesSolicitados => 'equipe.nova_rodada',
        default => 'equipe.adicionar_aprovacao',
    };
    $datas = collect([$etapa->entrada_em, $etapa->termino_em])->map(fn ($data) => $data?->format('d/m') ?? '—')->implode(' → ');
@endphp
<article id="fase-{{ $etapa->id }}" x-data="alternar" @if ($editando) x-init="abrir" @endif
    @class(['scroll-mt-24 overflow-hidden rounded-cartao bg-white shadow-cartao', 'ring-1 ring-azul/30' => $emCurso, 'opacity-80' => $etapa->oculta])>
    <div class="flex flex-wrap items-start gap-x-4 gap-y-3 p-5 sm:flex-nowrap sm:p-6">
        <span @class([
            'mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold',
            'bg-teal text-white' => $feita,
            'bg-ouro-claro/25 text-ouro ring-1 ring-ouro/40' => $etapa->estado === EstadoEtapa::AguardandoCliente,
            'bg-azul text-white' => in_array($etapa->estado, [EstadoEtapa::EmProducao, EstadoEtapa::AjustesEmAndamento], true),
            'bg-off text-mudo' => $etapa->estado === EstadoEtapa::AFazer,
        ])>
            @if ($feita)<x-icone nome="feito" class="h-4 w-4" />@else{{ $numero }}@endif
        </span>

        <div class="min-w-0 flex-1 basis-[calc(100%-3rem)] sm:basis-0">
            <div x-show="!aberto">
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5">
                    <h3 class="break-words text-[1.05rem] font-semibold">{{ $etapa->titulo }}</h3>
                    <x-etiqueta :tom="$etapa->estado->tom()">{{ $etapa->estado->rotuloEquipe() }}</x-etiqueta>
                    @if ($etapa->oculta)<x-etiqueta>{{ __('equipe.oculta') }}</x-etiqueta>@endif
                </div>
                <p class="mt-1.5 flex flex-wrap gap-x-5 gap-y-1 text-sm text-mudo">
                    <span class="inline-flex items-center gap-1.5"><x-icone nome="conta" class="h-4 w-4" />{{ $etapa->responsavel ?: '—' }}</span>
                    <span class="inline-flex items-center gap-1.5"><x-icone nome="calendario" class="h-4 w-4" />{{ $datas }}</span>
                </p>
                @if ($etapa->o_que_fazer)
                    <p class="mt-3 whitespace-pre-line text-[0.95rem]">{{ $etapa->o_que_fazer }}</p>
                @endif
            </div>

            <form method="POST" action="{{ route('equipe.etapas.update', $etapa) }}" x-show="aberto" x-cloak class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="_fase" value="{{ $etapa->id }}">
                <x-campos-fase :fase="$etapa" :prefixo="$prefixo" :isolado="! $editando" />
                <div class="flex flex-wrap gap-2">
                    <x-botao>{{ __('equipe.salvar') }}</x-botao>
                    <x-botao variante="discreto" type="button" x-on:click="fechar">{{ __('equipe.cancelar') }}</x-botao>
                </div>
            </form>
        </div>

        <div class="-ml-2 flex shrink-0 items-center gap-1 pl-12 sm:ml-0 sm:pl-0" x-show="!aberto">
            @can('iniciar', $etapa)
                <form method="POST" action="{{ route('equipe.etapas.iniciar', $etapa) }}">
                    @csrf
                    <x-botao variante="secundario" class="min-h-10 px-3.5 py-1.5 text-sm"><x-icone nome="iniciar" class="h-4 w-4" /><span class="hidden sm:inline">{{ __('equipe.iniciar') }}</span></x-botao>
                </form>
            @endcan
            @can('concluir', $etapa)
                <form method="POST" action="{{ route('equipe.etapas.concluir', $etapa) }}">
                    @csrf
                    <button class="inline-flex min-h-10 items-center gap-2 rounded-campo bg-teal px-3.5 py-1.5 font-titulo text-sm font-semibold text-white transition hover:bg-teal/90"><x-icone nome="feito" class="h-4 w-4" /><span class="hidden sm:inline">{{ __('equipe.concluir') }}</span></button>
                </form>
            @endcan
            <button type="button" class="flex min-h-10 min-w-10 items-center justify-center rounded-campo text-mudo transition hover:bg-off hover:text-tec" x-on:click="abrir" aria-label="{{ __('equipe.editar') }}" title="{{ __('equipe.editar') }}">
                <x-icone nome="editar" class="h-4 w-4" />
            </button>
            <details class="relative" x-data="confirmar">
                <summary class="flex min-h-10 min-w-10 cursor-pointer list-none items-center justify-center rounded-campo text-mudo transition hover:bg-off hover:text-tec [&::-webkit-details-marker]:hidden" aria-label="{{ __('equipe.mais_opcoes', ['fase' => $etapa->titulo]) }}">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="5" cy="12" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="19" cy="12" r="1.8"/></svg>
                </summary>
                <div class="absolute right-0 z-20 mt-2 w-60 rounded-cartao bg-white p-2 text-sm shadow-xl ring-1 ring-linha">
                    @foreach (['subir' => ! $primeira, 'descer' => ! $ultima] as $direcao => $pode)
                        @if ($pode)
                            <form method="POST" action="{{ route('equipe.etapas.mover', $etapa) }}">
                                @csrf
                                <input type="hidden" name="direcao" value="{{ $direcao }}">
                                <button class="flex min-h-10 w-full items-center gap-2 rounded-lg px-2.5 hover:bg-off"><x-icone :nome="$direcao" class="h-4 w-4 text-mudo" />{{ __('equipe.'.$direcao) }}</button>
                            </form>
                        @endif
                    @endforeach
                    <form method="POST" action="{{ route('equipe.etapas.visibilidade', $etapa) }}">
                        @csrf
                        <button class="flex min-h-10 w-full items-center gap-2 rounded-lg px-2.5 hover:bg-off"><x-icone :nome="$etapa->oculta ? 'olho' : 'olho-fechado'" class="h-4 w-4 text-mudo" />{{ __($etapa->oculta ? 'equipe.mostrar' : 'equipe.ocultar') }}</button>
                    </form>
                    @foreach (['reabrir' => ['reabrir', 'etapas.reabrir'], 'voltar_a_fazer' => ['voltarParaAFazer', 'etapas.voltar-a-fazer']] as $volta => [$permissao, $rota])
                        @can($permissao, $etapa)
                            <div x-data="confirmar">
                                <button type="button" class="flex min-h-10 w-full items-center gap-2 rounded-lg px-2.5 hover:bg-off" x-on:click="abrir" x-show="!aberto"><x-icone nome="voltar" class="h-4 w-4 text-mudo" />{{ __('equipe.'.$volta) }}</button>
                                <form method="POST" action="{{ route('equipe.'.$rota, $etapa) }}" x-show="aberto" x-cloak class="space-y-2 p-2">
                                    @csrf
                                    <p>{{ __('equipe.'.$volta.'_confirmar', ['fase' => $etapa->titulo]) }}</p>
                                    <div class="flex gap-2">
                                        <x-botao class="min-h-9 px-3 py-1 text-sm">{{ __('equipe.confirmar') }}</x-botao>
                                        <x-botao variante="secundario" type="button" class="min-h-9 px-3 py-1 text-sm" x-on:click="fechar">{{ __('equipe.cancelar') }}</x-botao>
                                    </div>
                                </form>
                            </div>
                        @endcan
                    @endforeach
                    @can('delete', $etapa)
                        <div class="mt-1 border-t border-linha pt-1">
                            <button type="button" class="flex min-h-10 w-full items-center gap-2 rounded-lg px-2.5 text-alerta hover:bg-alerta/5" x-on:click="abrir" x-show="!aberto"><x-icone nome="lixo" class="h-4 w-4" />{{ __('equipe.excluir') }}</button>
                            <form method="POST" action="{{ route('equipe.etapas.destroy', $etapa) }}" x-show="aberto" x-cloak class="space-y-2 p-2">
                                @csrf
                                @method('DELETE')
                                <p>{{ __('equipe.excluir_fase', ['fase' => $etapa->titulo]) }}</p>
                                <div class="flex gap-2">
                                    <x-botao variante="perigo" class="min-h-9 px-3 py-1 text-sm">{{ __('equipe.excluir') }}</x-botao>
                                    <x-botao variante="secundario" type="button" class="min-h-9 px-3 py-1 text-sm" x-on:click="fechar">{{ __('equipe.cancelar') }}</x-botao>
                                </div>
                            </form>
                        </div>
                    @endcan
                </div>
            </details>
        </div>
    </div>

    @if ($principal)
        <div class="space-y-3 border-t border-linha bg-off/50 px-5 py-5 sm:px-6">
            @if ($pedidoDoCliente)
                <x-card-aprovacao :card="$pedidoDoCliente" :total="$total" />
            @endif
            @if ($rascunhoFechado)
                <div x-data="alternar">
                    <div x-show="!aberto" class="flex flex-wrap items-center justify-between gap-3 rounded-campo border border-dashed border-tec/40 bg-white px-4 py-3">
                        <span class="flex items-center gap-2.5">
                            <span class="font-titulo text-sm font-semibold text-navy">{{ $rascunho->rodada($total) }}</span>
                            <x-etiqueta :tom="$rascunho->situacao->tom()">{{ $rascunho->situacao->rotulo() }}</x-etiqueta>
                        </span>
                        <x-botao class="min-h-10 py-1.5 text-sm" type="button" x-on:click="abrir"><x-icone nome="editar" class="h-4 w-4" />{{ __('equipe.abrir_card', ['rodada' => Str::lcfirst($rascunho->rodada($total))]) }}</x-botao>
                    </div>
                    <div x-show="aberto" x-cloak><x-card-aprovacao :card="$rascunho" :total="$total" /></div>
                </div>
            @else
                <x-card-aprovacao :card="$principal" :total="$total" />
            @endif
            @can('adicionarAprovacao', $etapa)
                <form method="POST" action="{{ route('equipe.aprovacoes.store', $etapa) }}">
                    @csrf
                    <x-botao class="min-h-10 py-1.5 text-sm"><x-icone nome="mais" class="h-4 w-4" />{{ __($rotuloNovoCard) }}</x-botao>
                </form>
            @endcan
            @if ($anteriores->isNotEmpty())
                <details class="group">
                    <summary class="inline-flex min-h-10 cursor-pointer list-none items-center gap-1.5 text-sm font-medium text-mudo hover:text-tec [&::-webkit-details-marker]:hidden">
                        <x-icone nome="seta" class="h-4 w-4 transition group-open:rotate-90" />{{ __('equipe.rodadas_anteriores', ['qtd' => $anteriores->count()]) }}
                    </summary>
                    <div class="mt-2 space-y-3">
                        @foreach ($anteriores as $anterior)
                            <x-card-aprovacao :card="$anterior" :total="$total" />
                        @endforeach
                    </div>
                </details>
            @endif
        </div>
    @else
        @can('adicionarAprovacao', $etapa)
            <form method="POST" action="{{ route('equipe.aprovacoes.store', $etapa) }}" class="-mt-2 px-5 pb-5 sm:pl-[4.5rem] sm:pr-6" x-show="!aberto">
                @csrf
                <button class="inline-flex min-h-10 items-center gap-1.5 rounded-campo border border-dashed border-tec/40 px-3.5 text-sm font-medium text-tec transition hover:border-tec hover:bg-azul-claro/50"><x-icone nome="mais" class="h-4 w-4" />{{ __('equipe.adicionar_aprovacao') }}</button>
            </form>
        @endcan
    @endif
</article>
