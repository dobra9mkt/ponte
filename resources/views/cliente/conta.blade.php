<x-layouts.cliente :titulo="__('ponte.conta.titulo')">
    <h1 class="text-2xl font-bold">{{ __('ponte.conta.titulo') }}</h1>

    <div class="mt-6 space-y-6">
        <x-cartao>
            <p class="text-sm text-mudo">{{ __('ponte.conta.email_acesso') }}</p>
            <p class="font-medium text-navy">{{ $usuario->email }}</p>
        </x-cartao>

        <x-cartao :titulo="__('ponte.conta.trocar_senha')">
            <form method="POST" action="{{ route('conta.senha') }}" class="space-y-5" novalidate>
                @csrf
                @method('PUT')
                <x-campo nome="senha_atual" rotulo="Senha atual" tipo="password" autocomplete="current-password" required />
                <x-campo nome="password" rotulo="Nova senha" tipo="password" autocomplete="new-password" :ajuda="__('ponte.senha.regra')" required />
                <x-campo nome="password_confirmation" rotulo="Repita a nova senha" tipo="password" autocomplete="new-password" required />
                <x-botao>Salvar nova senha</x-botao>
            </form>
        </x-cartao>

        <x-cartao :titulo="__('ponte.conta.sair_todos')">
            <p class="mb-4 text-mudo">{{ __('ponte.conta.sair_todos_texto') }}</p>
            <form method="POST" action="{{ route('conta.sair-todos') }}" class="space-y-5" novalidate>
                @csrf
                <x-campo nome="senha_confirmacao" rotulo="Confirme com a sua senha" tipo="password" autocomplete="current-password" required />
                <x-botao variante="secundario">{{ __('ponte.conta.sair_todos') }}</x-botao>
            </form>
        </x-cartao>
    </div>
</x-layouts.cliente>
