<x-layouts.simples titulo="Entrar">
    <h1 class="text-xl font-bold">{{ __('ponte.login.titulo') }}</h1>

    <form method="POST" action="{{ route('entrar') }}" class="mt-6 space-y-5" novalidate>
        @csrf
        <x-mensagens />
        <x-campo nome="email" rotulo="E-mail" tipo="email" autocomplete="username" inputmode="email" required autofocus />
        <x-campo nome="password" rotulo="Senha" tipo="password" autocomplete="current-password" required />

        <label class="flex min-h-11 cursor-pointer items-center gap-3 text-[0.95rem]">
            <input type="checkbox" name="lembrar" value="1" class="h-5 w-5 rounded border-linha text-azul focus:ring-azul" @checked(old('lembrar'))>
            {{ __('ponte.login.lembrar') }}
        </label>

        <x-botao largo>{{ __('ponte.login.entrar') }}</x-botao>
    </form>

    <p class="mt-5 text-center text-[0.95rem]">
        <a href="{{ route('senha.pedir') }}" class="link">{{ __('ponte.login.esqueci') }}</a>
    </p>
</x-layouts.simples>
