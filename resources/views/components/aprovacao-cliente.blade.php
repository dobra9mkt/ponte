@props(['entrega'])
@php
    $frente = $entrega->etapa->frente;
    $total = $frente->revisoes_incluidas;
    $rascunho = $entrega->rascunhos()->where('user_id', auth()->id())->first();
    $erros = $errors->hasAny(['texto', 'imagens', 'imagens.*', 'rascunho']);
    $versaoFinal = $entrega->ehVersaoFinal($total);
@endphp
<section {{ $attributes->class('portal-cartao overflow-hidden') }} x-data="confirmar">
    <div class="p-6 sm:p-8">
        <p class="text-xs font-semibold uppercase tracking-widest text-tec">{{ $versaoFinal ? __('fluxo.versao_final_pendente') : __('entregas.pendente').' · '.$entrega->rotuloParaCliente($total) }}</p>
        <h2 class="mt-3 break-words text-2xl font-semibold tracking-tight">{{ $entrega->titulo }}</h2>
        <p class="mt-2 text-sm text-mudo">{{ $frente->nomeParaCliente() }} · {{ __('entregas.enviada_em', ['data' => $entrega->publicada_em->format('d/m/Y')]) }} · {{ __('entregas.responder_ate', ['data' => $entrega->responder_ate->format('d/m/Y')]) }}</p>
        @if (! $versaoFinal && $entrega->ehUltimaRevisao($total))
            <x-aviso tipo="atencao" class="mt-5">{{ __('fluxo.ultima_revisao') }}</x-aviso>
        @endif
        <div class="mt-6"><x-material-entrega :entrega="$entrega" /></div>
        @if ($entrega->nota)
            <div class="mt-6">
                @if ($versaoFinal)
                    <h3 class="text-xs font-semibold uppercase tracking-widest text-tec">{{ __('fluxo.versao_final_o_que_fazer') }}</h3>
                @else
                    <h3 class="text-sm font-semibold">{{ __('entregas.observacoes_dobra') }}</h3>
                @endif
                <p class="mt-1 whitespace-pre-line text-[0.95rem]">{{ $entrega->nota }}</p>
            </div>
        @endif
        @error('entrega') <p role="alert" class="mt-5 text-sm font-medium text-alerta">{{ $message }}</p> @enderror
    </div>

    <div class="border-t border-linha/70 bg-off/40 p-6 sm:p-8">
        <form method="POST" action="{{ route('entregas.aprovar', $entrega) }}" class="space-y-4">
            @csrf
            <input type="hidden" name="confirmar" value="1">
            @if ($entrega->confirmacao)
                <label class="flex items-start gap-3 text-[0.95rem]">
                    <input type="checkbox" name="conferi" value="1" required class="mt-1 h-5 w-5 shrink-0 accent-azul">
                    <span>{{ $entrega->confirmacao }}</span>
                </label>
                @error('conferi') <p class="text-sm font-medium text-alerta">{{ $message }}</p> @enderror
            @endif
            <div class="flex flex-wrap gap-3" x-show="!aberto">
                <x-botao>{{ $entrega->textoDoBotaoAprovar($total) }}</x-botao>
                @unless ($versaoFinal)
                    <x-botao variante="secundario" type="button" x-on:click="abrir">{{ __('entregas.solicitar_ajustes') }}</x-botao>
                @endunless
            </div>
        </form>

        @unless ($versaoFinal)
        <form method="POST" action="{{ route('entregas.ajustes', $entrega) }}" enctype="multipart/form-data" class="space-y-5"
            @if (! $erros) x-show="aberto" x-cloak @endif
            x-data="rascunho" data-url="{{ route('entregas.rascunho', $entrega) }}" data-revisao="{{ $rascunho->revisao ?? 0 }}"
            data-salvando="{{ __('entregas.rascunho_salvando') }}" data-salvo="{{ __('entregas.rascunho_salvo') }}" data-erro="{{ __('entregas.rascunho_erro') }}">
            @csrf
            <x-campo nome="texto" :prefixo="'ajuste-'.$entrega->id" :rotulo="__('entregas.ajustes')" :valor="$rascunho?->texto" :ajuda="__('entregas.ajustes_ajuda')" :linhas="5" maxlength="10000" required x-ref="texto" x-on:input="agendar" />
            <p class="-mt-3 h-4 text-xs" x-bind:class="classeEstado" x-text="estado" aria-live="polite"></p>
            <x-campo nome="imagens[]" :prefixo="'ajuste-'.$entrega->id" tipo="file" :rotulo="__('entregas.imagens')" multiple accept="image/png,image/jpeg,image/webp" opcional />
            @foreach ($errors->get('imagens*') as $mensagens)
                <p class="text-sm font-medium text-alerta">{{ $mensagens[0] }}</p>
            @endforeach
            <div class="flex flex-wrap gap-3">
                <x-botao>{{ __('entregas.enviar_ajustes') }}</x-botao>
                <x-botao variante="discreto" type="button" x-on:click="fechar">{{ __('entregas.cancelar') }}</x-botao>
            </div>
        </form>
        @endunless
    </div>
</section>
