@php
    $pedido = $entrega->pedidos->last();
@endphp
<x-layouts.cliente :titulo="$entrega->titulo">
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('frentes.ver', $frente) }}" class="mb-6 inline-flex min-h-11 items-center gap-1 text-sm font-medium text-tec hover:underline"><x-icone nome="voltar" class="h-4 w-4" />{{ $frente->nomeParaCliente() }}</a>

        @if ($podeResponder)
            <x-aprovacao-cliente :entrega="$entrega" />
        @else
            @unless ($atual->is($entrega))
                <p class="mb-6 rounded-campo bg-off p-4 text-sm">
                    {{ __('entregas.antiga') }}
                    <a href="{{ route('entregas.ver', $atual) }}" class="font-semibold text-tec hover:underline">{{ __('entregas.ver_atual') }}</a>
                </p>
            @endunless
            <section class="portal-cartao p-6 sm:p-8">
                <p class="text-sm text-mudo">{{ $entrega->rotuloParaCliente($frente->revisoes_incluidas) }} · {{ __('entregas.enviada_em', ['data' => $entrega->publicada_em->format('d/m/Y')]) }}</p>
                <h1 class="mt-2 break-words text-2xl font-semibold tracking-tight">{{ $entrega->titulo }}</h1>
                <div class="mt-6"><x-material-entrega :entrega="$entrega" /></div>
                @if ($entrega->nota)
                    <div class="mt-6">
                        @if ($entrega->ehVersaoFinal($frente->revisoes_incluidas))
                            <h2 class="text-xs font-semibold uppercase tracking-widest text-tec">{{ __('fluxo.versao_final_o_que_fazer') }}</h2>
                        @else
                            <h2 class="text-sm font-semibold">{{ __('entregas.observacoes_dobra') }}</h2>
                        @endif
                        <p class="mt-1 whitespace-pre-line">{{ $entrega->nota }}</p>
                    </div>
                @endif
                <div class="mt-6 border-t border-linha/70 pt-5">
                    @if ($entrega->aprovacao)
                        <p class="font-medium text-teal">{{ __($entrega->ehVersaoFinal($frente->revisoes_incluidas) ? 'fluxo.versao_final_autorizado_em' : 'entregas.voce_aprovou', ['data' => $entrega->aprovacao->aprovado_em->format('d/m/Y'), 'hora' => $entrega->aprovacao->aprovado_em->format('H:i')]) }}</p>
                    @elseif ($pedido)
                        <p class="font-medium">{{ $pedido->situacao_extra === 'aguardando_orcamento' ? __('entregas.extra_enviado') : __('entregas.voce_pediu', ['data' => $pedido->created_at->format('d/m/Y')]) }}</p>
                        <p class="mt-2 whitespace-pre-line text-sm text-mudo">{{ $pedido->texto }}</p>
                        @foreach ($pedido->arquivos as $imagem)
                            <a href="{{ route('arquivos.ver', $imagem) }}" target="_blank" rel="noopener" class="mt-2 inline-flex min-h-11 items-center text-sm font-medium text-tec hover:underline">{{ $imagem->nome_original }}</a>
                        @endforeach
                        @if ($pedido->correcao_nossa_em)
                            <p class="mt-3 text-sm text-teal">{{ __('entregas.devolvida', ['motivo' => $pedido->correcao_nossa_motivo]) }}</p>
                        @endif
                    @endif
                </div>
            </section>
        @endif

        @if ($versoes->count() > 1)
            <details class="mt-8 border-t border-linha pt-2 text-sm text-mudo">
                <summary class="min-h-11 cursor-pointer py-3">{{ __('entregas.versoes_anteriores') }}</summary>
                <ul class="space-y-1">
                    @foreach ($versoes as $versao)
                        @continue($versao->is($entrega))
                        <li>
                            <a href="{{ route('entregas.ver', $versao) }}" class="inline-flex min-h-11 items-center text-tec hover:underline">
                                {{ $versao->rotuloParaCliente($frente->revisoes_incluidas) }} · {{ $versao->publicada_em->format('d/m/Y') }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </details>
        @endif
    </div>
</x-layouts.cliente>
