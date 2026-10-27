<x-layouts.cliente titulo="Boas-vindas">
    <div class="mx-auto max-w-2xl">
        <header class="text-center">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-azul-claro text-azul" aria-hidden="true"><x-icone nome="festa" class="h-6 w-6" /></span>
            <h1 class="mt-5 text-2xl font-semibold tracking-tight text-navy sm:text-3xl">
                {{ filled($nome) ? __('ponte.boas_vindas.titulo', ['nome' => $nome]) : __('ponte.boas_vindas.titulo_sem_nome') }}
            </h1>
            <p class="mx-auto mt-4 max-w-xl leading-relaxed text-mudo">{{ __('ponte.boas_vindas.abertura') }}</p>
        </header>

        <ol class="mt-10 space-y-4">
            @foreach (__('ponte.boas_vindas.cartoes') as $numero => $cartao)
                <li class="portal-cartao flex gap-4 p-5 sm:gap-5 sm:p-6">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-navy text-white" aria-hidden="true"><x-icone :nome="$cartao['icone']" class="h-5 w-5" /></span>
                    <div class="min-w-0">
                        <h2 class="font-titulo text-lg font-semibold text-navy"><span class="sr-only">{{ $numero + 1 }}. </span>{{ $cartao['titulo'] }}</h2>
                        <p class="mt-2 leading-relaxed text-mudo">{{ $cartao['texto'] }}</p>
                        @if ($loop->last && $whatsapp)
                            <x-botao variante="secundario" :href="$whatsapp" class="mt-4" rel="noopener noreferrer" target="_blank"><x-icone nome="whatsapp" class="h-5 w-5" />{{ __('ponte.boas_vindas.whatsapp') }}</x-botao>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>

        <form method="POST" action="{{ route('boas-vindas.concluir') }}" class="mt-10">
            @csrf
            <x-botao largo>{{ __('ponte.boas_vindas.botao') }}</x-botao>
        </form>
    </div>
</x-layouts.cliente>
