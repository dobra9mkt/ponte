<x-layouts.equipe titulo="Minha conta" :subtitulo="$usuario->name.' · '.$usuario->email">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        @if ($codigos)
            <x-cartao :titulo="__('ponte.equipe.recuperacao_titulo')" class="lg:col-span-2">
                <p class="mb-4 text-mudo">{{ __('ponte.equipe.recuperacao_texto') }}</p>
                <ul class="grid select-all grid-cols-2 gap-2 font-mono text-sm sm:grid-cols-4">
                    @foreach ($codigos as $codigo)
                        <li class="rounded-campo bg-off px-3 py-2 text-center text-navy">{{ $codigo }}</li>
                    @endforeach
                </ul>
            </x-cartao>
        @endif

        <x-cartao titulo="Trocar senha">
            <form method="POST" action="{{ route('equipe.conta.senha') }}" class="space-y-5" novalidate>
                @csrf
                @method('PUT')
                <x-campo nome="senha_atual" rotulo="Senha atual" tipo="password" autocomplete="current-password" required />
                <x-campo nome="password" rotulo="Nova senha" tipo="password" autocomplete="new-password" :ajuda="__('ponte.senha.regra')" required />
                <x-campo nome="password_confirmation" rotulo="Repita a nova senha" tipo="password" autocomplete="new-password" required />
                <x-botao>Salvar nova senha</x-botao>
            </form>
        </x-cartao>

        <x-cartao titulo="Verificação em duas etapas">
            <p class="mb-4 text-mudo">Ativa desde {{ $usuario->two_factor_confirmed_at?->format('d/m/Y') }}. Confirme a senha para ver ou trocar os códigos, ou para trocar de celular.</p>
            <form method="POST" action="{{ route('equipe.conta.codigos') }}" class="space-y-5" novalidate>
                @csrf
                <x-campo nome="senha_confirmacao" rotulo="Senha" tipo="password" autocomplete="current-password" required />
                <div class="flex flex-wrap gap-2">
                    <x-botao variante="secundario">Ver códigos</x-botao>
                    <x-botao variante="secundario" formaction="{{ route('equipe.conta.novos-codigos') }}">Gerar novos códigos</x-botao>
                    <x-botao variante="discreto" formaction="{{ route('equipe.conta.trocar-aplicativo') }}">Trocar de celular</x-botao>
                </div>
            </form>
        </x-cartao>

        <x-cartao titulo="Últimos acessos" class="lg:col-span-2">
            <x-atividades :atividades="$acessos" mostrar-ip />
        </x-cartao>
    </div>
</x-layouts.equipe>
