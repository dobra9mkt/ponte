@php
    use App\Enums\EstadoEtapa;
    use App\Models\Frente;
    $data = fn ($valor) => $valor?->format('d/m/Y') ?? '—';
@endphp
<x-layouts.equipe :titulo="$cliente->empresa" :subtitulo="$cliente->contato_nome" :voltar="['url' => route('equipe.consulta.index'), 'nome' => __('equipe.consulta_titulo')]">
    <div class="space-y-8">
        @forelse ($cliente->frentes as $frente)
            @php
                $fases = $frente->etapas;
                $atual = Frente::etapaAtual($fases);
                $prontas = $fases->filter(fn ($fase) => $fase->estado->finalizada())->count();
            @endphp
            <section class="overflow-hidden rounded-cartao bg-white shadow-cartao" aria-labelledby="servico-{{ $frente->id }}">
                <header class="border-b border-linha px-5 py-5 sm:px-6">
                    <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2">
                        <h2 id="servico-{{ $frente->id }}" class="text-lg font-semibold">{{ $frente->nome }}</h2>
                        @if ($atual)
                            <x-etiqueta :tom="$atual->estado->tom()">{{ __('equipe.agora', ['fase' => $atual->titulo]) }}</x-etiqueta>
                        @endif
                    </div>
                    <x-progresso :feitas="$prontas" :total="$fases->count()" class="mt-4" />
                    <p class="mt-2.5 flex flex-wrap justify-between gap-x-6 gap-y-1 text-sm text-mudo">
                        <span>{{ __('equipe.fases_concluidas', ['feitas' => $prontas, 'total' => $fases->count()]) }}</span>
                        <span>{{ __('equipe.inicio') }}: <strong class="font-medium text-navy">{{ $data($frente->inicio_em) }}</strong> · {{ __('equipe.termino_previsto') }}: <strong class="font-medium text-navy">{{ $data($frente->prazo_final) }}</strong></span>
                    </p>
                </header>

                <ol class="divide-y divide-linha/70">
                    @forelse ($fases as $fase)
                        @php
                            $feita = $fase->estado->finalizada();
                            $aFazer = $fase->estado === EstadoEtapa::AFazer;
                            $temDatas = $fase->entrada_em || $fase->termino_em;
                        @endphp
                        <li @class(['flex gap-4 px-5 sm:px-6', 'py-4' => ! $aFazer, 'py-3' => $aFazer, 'bg-azul-claro/30' => $fase->is($atual)])>
                            <span @class([
                                'mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold',
                                'bg-teal text-white' => $feita,
                                'bg-ouro-claro/25 text-ouro ring-1 ring-ouro/40' => $fase->estado === EstadoEtapa::AguardandoCliente,
                                'bg-azul text-white' => in_array($fase->estado, [EstadoEtapa::EmProducao, EstadoEtapa::AjustesEmAndamento, EstadoEtapa::AjusteExtraSolicitado], true),
                                'bg-off text-mudo' => $aFazer,
                            ])>
                                @if ($feita)<x-icone nome="feito" class="h-3.5 w-3.5" />@else{{ $loop->iteration }}@endif
                            </span>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2">
                                    <h3 @class(['font-semibold', 'text-mudo' => $aFazer, 'text-navy' => ! $aFazer])>{{ $fase->titulo }}</h3>
                                    <x-etiqueta :tom="$fase->estado->tom()">{{ $fase->estado->rotuloEquipe() }}</x-etiqueta>
                                </div>
                                @if ($fase->responsavel || $temDatas)
                                    <p class="mt-1 flex flex-wrap gap-x-5 gap-y-1 text-sm text-mudo">
                                        @if ($fase->responsavel)
                                            <span class="inline-flex items-center gap-1.5"><x-icone nome="conta" class="h-4 w-4" />{{ $fase->responsavel }}</span>
                                        @endif
                                        @if ($temDatas)
                                            <span class="inline-flex items-center gap-1.5"><x-icone nome="calendario" class="h-4 w-4" />{{ $data($fase->entrada_em) }} → {{ $data($fase->termino_em) }}</span>
                                        @endif
                                    </p>
                                @endif

                                @if ($fase->entregasPublicadas->isNotEmpty())
                                    <ul class="mt-3 flex flex-wrap gap-2" aria-label="{{ __('equipe.historico_aprovacoes') }}">
                                        @foreach ($fase->entregasPublicadas->sortBy('versao') as $card)
                                            <li>
                                                <x-etiqueta :tom="$card->estaVencida() ? 'alerta' : $card->situacao->tom()">
                                                    {{ $card->rodada($frente->revisoes_incluidas) }} · {{ $card->rotuloSituacao($frente->revisoes_incluidas) }}{{ $card->aprovacao ? ' '.$card->aprovacao->aprovado_em->format('d/m') : '' }}
                                                </x-etiqueta>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="px-6 py-5 text-sm text-mudo">{{ __('equipe.sem_fases') }}</li>
                    @endforelse
                </ol>
            </section>
        @empty
            <p class="rounded-cartao bg-white px-6 py-10 text-center text-mudo shadow-cartao">{{ __('equipe.sem_servicos') }}</p>
        @endforelse
    </div>
</x-layouts.equipe>
