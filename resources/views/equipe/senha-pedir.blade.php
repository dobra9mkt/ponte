<x-layouts.simples titulo="Esqueci minha senha" discreto>
    <h1 class="text-xl font-bold">{{ __('ponte.senha.pedir_titulo') }}</h1>
    <p class="mt-2 text-mudo">{{ __('ponte.senha.pedir_texto') }}</p>

    <form method="POST" action="{{ route('equipe.senha.pedir') }}" class="mt-6 space-y-5" novalidate>
        @csrf
        <x-mensagens />
        <x-campo nome="email" rotulo="E-mail" tipo="email" autocomplete="username" inputmode="email" required autofocus />
        <x-botao largo>{{ __('ponte.senha.pedir_botao') }}</x-botao>
    </form>

    <p class="mt-5 text-center text-[0.95rem]">
        <a href="{{ route('equipe.entrar') }}" class="link">Voltar para entrar</a>
    </p>
</x-layouts.simples>
