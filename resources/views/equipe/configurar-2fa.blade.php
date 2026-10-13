<x-layouts.simples titulo="Proteja sua conta" discreto>
    <h1 class="text-xl font-bold">{{ __('ponte.equipe.configurar_titulo') }}</h1>
    <p class="mt-2 text-mudo">{{ __('ponte.equipe.configurar_texto') }}</p>

    <div class="mt-6 flex justify-center">
        <img src="{{ $qr }}" alt="QR code para o aplicativo autenticador" class="h-48 w-48 rounded-campo border border-linha p-2">
    </div>

    <p class="mt-4 text-sm text-mudo">{{ __('ponte.equipe.chave_manual') }}</p>
    <p class="mt-1 select-all break-all rounded-campo bg-off px-3 py-2 font-mono text-sm text-navy">{{ $chave }}</p>

    <form method="POST" action="{{ route('equipe.2fa.configurar') }}" class="mt-6 space-y-5" novalidate>
        @csrf
        <x-campo nome="codigo" rotulo="Código do aplicativo" inputmode="numeric" autocomplete="one-time-code" maxlength="6" pattern="[0-9]*" class="text-center font-titulo text-2xl tracking-[0.4em]" required />
        <x-botao largo>Ativar verificação</x-botao>
    </form>

    <x-slot:rodape>
        <form method="POST" action="{{ route('sair') }}">
            @csrf
            <button class="underline-offset-4 hover:underline">Sair</button>
        </form>
    </x-slot:rodape>
</x-layouts.simples>
