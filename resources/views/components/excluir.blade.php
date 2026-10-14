@props(['acao', 'rotulo', 'mensagem', 'confirmar' => null])
<div x-data="confirmar" {{ $attributes }}>
    <button type="button" x-on:click="abrir" x-show="!aberto" class="inline-flex min-h-11 items-center gap-2 text-sm font-medium text-mudo underline-offset-4 hover:text-alerta hover:underline">
        <x-icone nome="lixo" class="h-4 w-4" />{{ $rotulo }}
    </button>
    <form method="POST" action="{{ $acao }}" x-show="aberto" x-cloak class="rounded-cartao border border-alerta/30 bg-white p-4">
        @csrf
        @method('DELETE')
        <p class="text-sm">{{ $mensagem }}</p>
        <div class="mt-3 flex flex-wrap gap-2">
            <x-botao variante="perigo">{{ $confirmar ?? __('equipe.excluir') }}</x-botao>
            <x-botao variante="secundario" type="button" x-on:click="fechar">{{ __('equipe.cancelar') }}</x-botao>
        </div>
    </form>
</div>
