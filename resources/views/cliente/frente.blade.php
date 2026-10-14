@php
    use App\Enums\EstadoEtapa;
    use App\Enums\SituacaoCard;
    $prontas = $etapas->filter(fn ($etapa) => $etapa->estado->finalizada())->count();
    $periodo = fn ($etapa) => $etapa->entrada_em || $etapa->termino_em
        ? __('fluxo.periodo', ['inicio' => $etapa->entrada_em?->format('d/m') ?? '—', 'fim' => $etapa->termino_em?->format('d/m') ?? '—'])
        : __('fluxo.a_combinar');
@endphp
<x-layouts.cliente :titulo="$frente->nomeParaCliente()">

    <x-boas-vindas class="mb-5" />

    <section class="portal-cartao mb-12 p-6 sm:p-8">
        @if ($servicos->count() > 1)
            <nav aria-label="{{ __('fluxo.seus_projetos') }}" class="mb-7">
                <ul class="flex flex-col gap-1 rounded-xl bg-off p-1 sm:inline-flex sm:max-w-full sm:flex-row sm:flex-wrap">
                    @foreach ($servicos as $servico)
                        @php
                            $atual = $servico->is($frente);
                        @endphp
                        <li>
                            <a href="{{ route('frentes.ver', $servico) }}" aria-current="{{ $atual ? 'page' : 'false' }}" @class([
                                'flex min-h-9 items-center justify-between gap-2 rounded-lg px-3.5 font-titulo text-sm font-semibold transition sm:inline-flex sm:justify-start',
                                'bg-white text-navy shadow-sm ring-1 ring-linha/60' => $atual,
                                'text-mudo hover:text-navy' => ! $atual,
                            ])>
                                {{ $servico->nomeParaCliente() }}
                                @if ($servico->aguardando)
                                    <span class="h-2 w-2 rounded-full bg-ouro-claro" title="{{ __('fluxo.aprovacao_pendente') }}"></span><span class="sr-only">{{ __('fluxo.aprovacao_pendente') }}</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        @endif

        <h1 class="break-words text-2xl font-semibold tracking-tight sm:text-3xl">{{ $frente->nomeParaCliente() }}</h1>
        <x-progresso :feitas="$prontas" :total="$etapas->count()" class="mt-5" />
        <div class="mt-3 flex flex-wrap justify-between gap-x-6 gap-y-1 text-sm text-mudo">
            <span>{{ __('fluxo.progresso', ['feitas' => $prontas, 'total' => $etapas->count()]) }}</span>
            <span>{{ __('fluxo.previsao') }}: <strong class="font-semibold text-navy">{{ $frente->prazo_final?->format('d/m/Y') ?? __('fluxo.a_combinar') }}</strong></span>
        </div>
    </section>

    <ol class="relative mx-auto max-w-3xl" aria-label="{{ __('fluxo.caminho') }}">
        <span class="absolute bottom-6 left-[1.125rem] top-6 w-px bg-linha" aria-hidden="true"></span>

        <li class="relative flex items-center gap-5 pb-8">
            <span class="relative z-10 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-navy text-white"><x-icone nome="iniciar" class="h-4 w-4" /></span>
            <p class="font-titulo font-semibold text-navy">
                {{ $frente->inicio_em ? __('fluxo.comecamos_em', ['data' => $frente->inicio_em->format('d/m/Y')]) : __('fluxo.comeco') }}
            </p>
        </li>

        @foreach ($etapas as $etapa)
            @php
                $feita = $etapa->estado->finalizada();
                $suaVez = $etapa->estado === EstadoEtapa::AguardandoCliente;
                $emCurso = in_array($etapa->estado, [EstadoEtapa::EmProducao, EstadoEtapa::AjustesEmAndamento], true);
            @endphp
            <li class="relative flex gap-5 pb-8" @if ($suaVez || $emCurso) aria-current="step" @endif>
                <span @class([
                    'relative z-10 flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-xs font-semibold',
                    'bg-teal text-white' => $feita,
                    'bg-ouro-claro text-navy ring-4 ring-ouro-claro/30' => $suaVez,
                    'bg-azul text-white ring-4 ring-azul/15' => $emCurso,
                    'border border-linha bg-white text-mudo' => ! $feita && ! $suaVez && ! $emCurso,
                ])>
                    @if ($feita)<x-icone nome="feito" class="h-4 w-4" />@else{{ $loop->iteration }}@endif
                </span>

                <div @class(['min-w-0 flex-1', 'portal-cartao p-5' => $suaVez || $emCurso, 'pt-1' => ! $suaVez && ! $emCurso])>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                        <h2 @class(['break-words text-lg font-semibold', 'text-mudo' => ! $feita && ! $suaVez && ! $emCurso])>{{ $etapa->titulo }}</h2>
                        <x-etiqueta :tom="$etapa->estado->tom()">{{ $etapa->estado->rotuloCliente() }}</x-etiqueta>
                    </div>
                    <p class="mt-1 flex items-center gap-1.5 text-sm text-mudo"><x-icone nome="calendario" class="h-4 w-4" />{{ $periodo($etapa) }}</p>
                    @if ($etapa->o_que_fazer && ! $feita)
                        <p class="mt-3 whitespace-pre-line text-[0.95rem]">{{ $etapa->o_que_fazer }}</p>
                    @endif

                    @if ($etapa->entregasPublicadas->isNotEmpty())
                        <div @if ($feita) x-data="alternar" @endif>
                        @if ($feita)
                            <button type="button" x-on:click="alternar" class="mt-2 inline-flex min-h-10 items-center gap-1.5 text-sm font-semibold text-tec hover:underline">
                                <x-icone nome="seta" class="h-4 w-4 transition" x-bind:class="classeSeta" />
                                <span x-show="!aberto">{{ __('fluxo.ver_historico', ['qtd' => $etapa->entregasPublicadas->count()]) }}</span>
                                <span x-show="aberto" x-cloak>{{ __('fluxo.ocultar_historico') }}</span>
                            </button>
                        @endif
                        <ul class="mt-4 space-y-2" @if ($feita) x-show="aberto" x-cloak @endif>
                            @foreach ($etapa->entregasPublicadas as $card)
                                @php
                                    $pendente = $card->situacao === SituacaoCard::AguardandoCliente;
                                    $final = $card->ehVersaoFinal($frente->revisoes_incluidas);
                                @endphp
                                <li @class([
                                    'flex flex-wrap items-center gap-x-3 gap-y-2 rounded-campo px-4 py-3 text-sm',
                                    'bg-teal/10 ring-1 ring-teal/30' => $final,
                                    'bg-ouro-claro/15 ring-1 ring-ouro/30' => $pendente && ! $final,
                                    'bg-off' => ! $pendente && ! $final,
                                ])>
                                    @if ($final)
                                        <p class="flex w-full items-start gap-3 pb-1 font-titulo text-base font-semibold leading-snug text-navy">
                                            <x-icone nome="festa" class="mt-0.5 h-5 w-5 shrink-0 text-teal" />
                                            <span>{{ __('fluxo.versao_final_celebracao') }}</span>
                                        </p>
                                    @endif
                                    <span class="font-semibold text-navy">{{ $card->rotuloParaCliente($frente->revisoes_incluidas) }}</span>
                                    <span class="text-mudo">{{ __('fluxo.revisao_enviada', ['data' => $card->publicada_em->format('d/m')]) }}</span>
                                    <span @class(['font-medium', 'text-teal' => $card->situacao === SituacaoCard::Aprovado, 'text-ouro' => $pendente && ! $final, 'text-mudo' => $pendente && $final, 'text-tec' => $card->situacao === SituacaoCard::AjustesSolicitados])>
                                        @switch($card->situacao)
                                            @case(SituacaoCard::Aprovado){{ __($final ? 'fluxo.versao_final_autorizada_em' : 'fluxo.revisao_aprovada', ['data' => $card->aprovacao?->aprovado_em->format('d/m')]) }}@break
                                            @case(SituacaoCard::AjustesSolicitados){{ __('fluxo.revisao_ajustes', ['data' => $card->pedido?->created_at->format('d/m')]) }}@break
                                            @default{{ __('fluxo.responder_ate', ['data' => $card->responder_ate?->format('d/m')]) }}
                                        @endswitch
                                    </span>
                                    <a href="{{ route('entregas.ver', $card) }}" @class([
                                        'ml-auto inline-flex min-h-10 items-center gap-1.5 rounded-campo px-3 font-semibold',
                                        'bg-azul text-white hover:bg-azul-hover' => $pendente,
                                        'text-tec hover:bg-white' => ! $pendente,
                                    ])>{{ __($pendente && ! $final ? 'fluxo.revisar' : 'fluxo.ver') }}<x-icone nome="seta" class="h-4 w-4" /></a>
                                    @if ($pendente && $card->ehUltimaRevisao($frente->revisoes_incluidas))
                                        <p class="w-full text-sm font-medium text-ouro">{{ __('fluxo.ultima_revisao') }}</p>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        </div>
                    @endif
                </div>
            </li>
        @endforeach

        @if ($frente->prazo_final)
            <li class="relative flex items-center gap-5">
                <span class="relative z-10 flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-dashed border-tec/50 bg-white text-tec"><x-icone nome="calendario" class="h-4 w-4" /></span>
                <p class="text-sm text-mudo">{{ __('fluxo.termino_previsto', ['data' => $frente->prazo_final->format('d/m/Y')]) }}</p>
            </li>
        @endif
    </ol>
</x-layouts.cliente>
