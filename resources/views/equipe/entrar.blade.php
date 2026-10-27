<x-layouts.simples titulo="Entrar" discreto>
    <h1 class="text-xl font-bold">Entrar</h1>

    <form method="POST" action="{{ route('equipe.entrar') }}" class="mt-6 space-y-5" novalidate>
        @csrf
        <x-mensagens />
        <x-campo nome="email" rotulo="E-mail" tipo="email" autocomplete="username" inputmode="email" required autofocus />
        <x-campo nome="password" rotulo="Senha" tipo="password" autocomplete="current-password" required />
        <x-botao largo>{{ __('ponte.equipe.entrar') }}</x-botao>
    </form>

    <p class="mt-5 text-center text-[0.95rem]">
        <a href="{{ route('equipe.senha.pedir') }}" class="link">{{ __('ponte.login.esqueci') }}</a>
    </p>
</x-layouts.simples>
