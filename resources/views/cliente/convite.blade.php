<x-layouts.simples titulo="Crie sua senha">
    <h1 class="text-xl font-bold">{{ __('ponte.convite.titulo') }}</h1>
    <p class="mt-2 text-mudo">{{ __('ponte.convite.texto', ['empresa' => $empresa]) }}</p>

    <form method="POST" action="{{ route('convite', $token) }}" class="mt-6 space-y-5" novalidate>
        @csrf
        <x-campo nome="password" rotulo="Senha" tipo="password" autocomplete="new-password" :ajuda="__('ponte.senha.regra')" required autofocus />
        <x-campo nome="password_confirmation" rotulo="Repita a senha" tipo="password" autocomplete="new-password" required />
        <x-botao largo>{{ __('ponte.convite.botao') }}</x-botao>
    </form>
</x-layouts.simples>
