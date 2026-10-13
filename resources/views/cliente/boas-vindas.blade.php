<x-layouts.cliente titulo="Boas-vindas">
    <h1 class="text-2xl font-bold">{{ __('ponte.boas_vindas.titulo') }}</h1>

    <ol class="mt-6 space-y-4">
        @foreach (__('ponte.boas_vindas.cartoes') as $numero => $cartao)
            <li class="flex gap-4 rounded-cartao bg-white p-5 shadow-cartao">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-azul-claro font-titulo font-bold text-azul">{{ $numero + 1 }}</span>
                <div>
                    <h2 class="font-semibold">{{ $cartao['titulo'] }}</h2>
                    <p class="mt-1 text-mudo">{{ $cartao['texto'] }}</p>
                </div>
            </li>
        @endforeach
    </ol>

    <form method="POST" action="{{ route('boas-vindas.concluir') }}" class="mt-8">
        @csrf
        <x-botao largo>{{ __('ponte.boas_vindas.botao') }}</x-botao>
    </form>
</x-layouts.cliente>
