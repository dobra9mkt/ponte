@props(['card', 'total'])
@inject('diasUteis', 'App\Servicos\DiasUteis')
@php
    use App\Enums\SituacaoCard;
    $prefixo = 'card-'.$card->id.'-';
    $comErro = (string) old('_card') === (string) $card->id;
    $vencido = $card->estaVencida();
    $versaoFinal = $card->ehVersaoFinal($total);
    $links = $comErro ? array_values(old('links', [])) : $card->links;
    $erros = $comErro ? collect(['card', 'nota', 'texto_aprovar', 'links.*', 'arquivos', 'arquivos.*', 'remover.*'])->flatMap(fn ($chave) => $errors->get($chave))->flatten() : collect();
@endphp
<div id="card-{{ $card->id }}" {{ $attributes->class('scroll-mt-24 rounded-campo border border-linha bg-white') }}>
    <header class="flex flex-wrap items-center justify-between gap-x-4 gap-y-1 border-b border-linha px-4 py-3">
        <span class="flex items-center gap-2.5">
            <span class="font-titulo text-sm font-semibold text-navy">{{ $card->rodada($total) }}</span>
            <x-etiqueta :tom="$vencido ? 'alerta' : $card->situacao->tom()">{{ $vencido ? __('equipe.prazo_vencido', ['data' => $card->responder_ate->format('d/m')]) : $card->rotuloSituacao($total) }}</x-etiqueta>
        </span>
        @if ($card->publicada_em)
            <span class="text-xs text-mudo">{{ __('equipe.mostrado_em', ['data' => $card->publicada_em->format('d/m/Y')]) }}</span>
        @endif
    </header>

    @if ($card->situacao === SituacaoCard::Rascunho)
        <form method="POST" action="{{ route('equipe.aprovacoes.update', $card) }}" enctype="multipart/form-data" class="space-y-5 p-4 sm:p-5">
            @csrf
            @method('PUT')
            <input type="hidden" name="_card" value="{{ $card->id }}">

            <x-campo nome="nota" :rotulo="__($versaoFinal ? 'equipe.mensagem_cliente' : 'equipe.o_que_revisar')" :valor="$card->nota" :prefixo="$prefixo" :isolado="! $comErro" :linhas="3" maxlength="5000" />
            @if ($versaoFinal)
                <x-campo nome="texto_aprovar" :rotulo="__('equipe.botao_aprovacao')" :valor="$card->texto_aprovar ?? __('fluxo.versao_final_botao')" :prefixo="$prefixo" :isolado="! $comErro" maxlength="60" />
            @endif

            <fieldset class="space-y-2">
                <legend class="mb-1.5 text-[0.95rem] font-medium text-navy">{{ __('equipe.anexos') }}</legend>
                @foreach ($card->arquivos as $arquivo)
                    <label class="flex min-h-11 items-center gap-3 rounded-campo bg-off px-3 py-2 text-sm has-[:checked]:opacity-50">
                        <x-icone nome="clipe" class="h-4 w-4 text-tec" />
                        <a href="{{ route('equipe.arquivos.ver', $arquivo) }}" target="_blank" rel="noopener" class="min-w-0 flex-1 truncate font-medium text-navy hover:underline">{{ $arquivo->nome_original }}</a>
                        <input type="checkbox" name="remover[]" value="{{ $arquivo->id }}" class="h-4 w-4 rounded border-linha text-alerta">
                        <span class="text-mudo">{{ __('equipe.remover') }}</span>
                    </label>
                @endforeach
                <input id="{{ $prefixo }}arquivos" type="file" name="arquivos[]" multiple aria-label="{{ __('equipe.anexar') }}"
                    class="block w-full text-sm text-mudo file:mr-3 file:min-h-10 file:cursor-pointer file:rounded-lg file:border-0 file:bg-azul-claro file:px-4 file:font-medium file:text-tec hover:file:bg-azul-claro/70">
            </fieldset>

            <fieldset class="space-y-2" x-data="alternar">
                <legend class="mb-1.5 text-[0.95rem] font-medium text-navy">{{ __('equipe.links') }}</legend>
                @foreach ([...$links, ...array_fill(0, 4, ['rotulo' => '', 'url' => ''])] as $indice => $link)
                    @php($extra = $indice > count($links))
                    <div class="grid gap-2 sm:grid-cols-[12rem_minmax(0,1fr)]" @if ($extra) x-show="aberto" x-cloak @endif>
                        <input name="links[{{ $indice }}][rotulo]" value="{{ $link['rotulo'] ?? '' }}" placeholder="{{ __('equipe.nome_link') }}" aria-label="{{ __('equipe.nome_link') }}" maxlength="100" class="campo">
                        <input name="links[{{ $indice }}][url]" type="url" value="{{ $link['url'] ?? '' }}" placeholder="{{ __('equipe.endereco_link') }}" aria-label="{{ __('equipe.links') }}" maxlength="2000" class="campo">
                    </div>
                @endforeach
                <button type="button" class="inline-flex min-h-10 items-center gap-1.5 text-sm font-medium text-tec hover:underline" x-on:click="abrir" x-show="!aberto">
                    <x-icone nome="mais" class="h-4 w-4" />{{ __('equipe.outro_link') }}
                </button>
            </fieldset>

            @foreach ($erros as $mensagem)
                <p role="alert" class="text-sm font-medium text-alerta">{{ $mensagem }}</p>
            @endforeach

            <div class="flex flex-wrap items-center gap-2 border-t border-linha pt-4">
                <x-botao variante="secundario">{{ __('equipe.salvar_rascunho') }}</x-botao>
                <x-botao name="mostrar" value="1"><x-icone nome="enviar" class="h-4 w-4" />{{ __('equipe.mostrar_cliente') }}</x-botao>
                <span class="text-sm text-mudo sm:ml-auto">{{ __('equipe.prazo_previsto', ['data' => $diasUteis->somar(today(), config()->integer('ponte.prazo_resposta_dias_uteis'))->format('d/m')]) }}</span>
            </div>
        </form>
        <form method="POST" action="{{ route('equipe.aprovacoes.destroy', $card) }}" class="px-4 pb-4 sm:px-5">
            @csrf
            @method('DELETE')
            <button class="text-sm text-mudo underline-offset-4 hover:text-alerta hover:underline">{{ __('equipe.excluir_rascunho') }}</button>
        </form>
    @else
        <div class="space-y-4 p-4 sm:p-5">
            <p class="whitespace-pre-line text-[0.95rem]">{{ $card->nota }}</p>
            @if ($card->arquivos->isNotEmpty() || $card->links)
                <x-material-entrega :entrega="$card" rota="equipe.arquivos.ver" />
            @endif

            @if ($card->situacao === SituacaoCard::AguardandoCliente)
                <div x-data="alternar" class="flex flex-wrap items-center gap-x-3 gap-y-2 text-sm">
                    <span @class(['font-medium', 'text-alerta' => $vencido, 'text-navy' => ! $vencido])>{{ __('equipe.prazo', ['data' => $card->responder_ate->format('d/m/Y')]) }}</span>
                    <button type="button" class="font-medium text-tec hover:underline" x-on:click="abrir" x-show="!aberto">{{ __('equipe.alterar_prazo') }}</button>
                    <form method="POST" action="{{ route('equipe.aprovacoes.prazo', $card) }}" class="flex flex-wrap items-center gap-2" x-show="aberto" x-cloak>
                        @csrf
                        @method('PUT')
                        <input type="date" name="responder_ate" value="{{ $card->responder_ate->toDateString() }}" min="{{ today()->toDateString() }}" required aria-label="{{ __('equipe.alterar_prazo') }}" class="campo-mini">
                        <x-botao class="min-h-10 px-4 py-1.5 text-sm">{{ __('equipe.salvar') }}</x-botao>
                        <x-botao variante="discreto" type="button" class="min-h-10 px-3 py-1.5 text-sm" x-on:click="fechar">{{ __('equipe.cancelar') }}</x-botao>
                    </form>
                </div>
                @error('responder_ate') <p role="alert" class="text-sm font-medium text-alerta">{{ $message }}</p> @enderror
            @elseif ($card->situacao === SituacaoCard::Aprovado && $card->aprovacao)
                <p class="text-sm font-medium text-teal">{{ __($versaoFinal ? 'fluxo.versao_final_autorizado_em' : 'equipe.aprovado_em', ['data' => $card->aprovacao->aprovado_em->format('d/m/Y'), 'hora' => $card->aprovacao->aprovado_em->format('H:i')]) }}</p>
            @elseif ($card->situacao === SituacaoCard::AjustesSolicitados && $card->pedido)
                <div class="rounded-campo border-l-4 border-azul bg-azul-claro/60 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-tec">{{ __('equipe.ajustes_em', ['data' => $card->pedido->created_at->format('d/m/Y')]) }}</p>
                    <p class="mt-2 whitespace-pre-line text-[0.95rem]">{{ $card->pedido->texto }}</p>
                    @foreach ($card->pedido->arquivos as $imagem)
                        <a href="{{ route('equipe.arquivos.ver', $imagem) }}" target="_blank" rel="noopener" class="mr-3 mt-2 inline-flex min-h-10 items-center gap-1.5 text-sm font-medium text-tec hover:underline"><x-icone nome="clipe" class="h-4 w-4" />{{ $imagem->nome_original }}</a>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>
