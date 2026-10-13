<x-layouts.simples titulo="Nova senha">
    <h1 class="text-xl font-bold">{{ __('ponte.senha.redefinir_titulo') }}</h1>

    <form method="POST" action="{{ route('senha.salvar') }}" class="mt-6 space-y-5" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <x-campo nome="email" rotulo="E-mail" tipo="email" :valor="$email" autocomplete="username" required />
        <x-campo nome="password" rotulo="Nova senha" tipo="password" autocomplete="new-password" :ajuda="__('ponte.senha.regra')" required />
        <x-campo nome="password_confirmation" rotulo="Repita a nova senha" tipo="password" autocomplete="new-password" required />
        <x-botao largo>{{ __('ponte.senha.redefinir_botao') }}</x-botao>
    </form>
</x-layouts.simples>
