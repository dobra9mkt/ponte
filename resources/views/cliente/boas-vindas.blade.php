<x-layouts.cliente titulo="Boas-vindas">
    <x-slot:topo>
        <section class="bg-navy text-white">
            <div class="mx-auto max-w-3xl px-5 pb-24 pt-8 text-center sm:px-8 sm:pb-28 sm:pt-12">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-white/10 text-white" aria-hidden="true"><x-icone nome="festa" class="h-6 w-6" /></span>
                <h1 class="mt-5 text-2xl font-semibold tracking-tight text-white sm:text-3xl">
                    {{ filled($nome) ? __('ponte.boas_vindas.titulo', ['nome' => $nome]) : __('ponte.boas_vindas.titulo_sem_nome') }}
                </h1>
                <p class="mx-auto mt-3 max-w-xl leading-relaxed text-[#cdd8e6]">{{ __('ponte.boas_vindas.abertura') }}</p>
            </div>
        </section>
    </x-slot:topo>

    <ol class="-mt-[5.5rem] grid gap-4 sm:-mt-[6.5rem] sm:grid-cols-3">
        @foreach (__('ponte.boas_vindas.cartoes') as $cartao)
            <li class="portal-cartao flex flex-col p-6">
                <h2 class="font-titulo text-navy">
                    @isset($cartao['destaque'])
                        <span class="block text-5xl font-bold leading-none text-azul">{{ $cartao['destaque'] }}</span>
                    @else
                        <span class="flex h-12 w-12 items-center justify-center rounded-full bg-azul-claro text-azul" aria-hidden="true"><x-icone :nome="$cartao['icone']" class="h-6 w-6" /></span>
                    @endisset
                    <span class="mt-3 block text-lg font-semibold leading-snug">{{ $cartao['titulo'] }}</span>
                </h2>
                <p class="mt-2 leading-relaxed text-mudo">{{ $cartao['texto'] }}</p>
            </li>
        @endforeach
    </ol>

    <div class="mt-10 flex flex-col items-center gap-5">
        <form method="POST" action="{{ route('boas-vindas.concluir') }}">
            @csrf
            <x-botao>{{ __('ponte.boas_vindas.botao') }}</x-botao>
        </form>
        @if ($whatsapp)
            <a href="{{ $whatsapp }}" class="link inline-flex min-h-11 items-center gap-2 text-sm font-medium" rel="noopener noreferrer" target="_blank">
                <x-icone nome="whatsapp" class="h-5 w-5" />{{ __('ponte.boas_vindas.whatsapp') }}
            </a>
        @endif
    </div>
</x-layouts.cliente>
