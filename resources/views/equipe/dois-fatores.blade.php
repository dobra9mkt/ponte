<x-layouts.simples titulo="Código de verificação" discreto>
    <h1 class="text-xl font-bold">{{ __('ponte.equipe.codigo_titulo') }}</h1>
    <p class="mt-2 text-mudo">{{ __('ponte.equipe.codigo_texto') }}</p>

    <form method="POST" action="{{ route('equipe.2fa') }}" class="mt-6 space-y-5" novalidate>
        @csrf
        <x-campo nome="codigo" rotulo="Código" inputmode="numeric" autocomplete="one-time-code" maxlength="6" pattern="[0-9]*" class="text-center font-titulo text-2xl tracking-[0.4em]" autofocus />

        <details class="text-[0.95rem]">
            <summary class="link cursor-pointer">{{ __('ponte.equipe.usar_recuperacao') }}</summary>
            <div class="mt-3">
                <x-campo nome="codigo_recuperacao" rotulo="Código de recuperação" autocomplete="off" />
            </div>
        </details>

        <x-botao largo>Confirmar</x-botao>
    </form>
</x-layouts.simples>
