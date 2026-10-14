@php
    $dados = [
        'contato' => $cliente?->contato_nome,
        'empresa' => $cliente?->empresa,
        'email' => $usuario->email,
        'telefone' => $cliente?->telefone,
        'endereco' => $cliente?->endereco,
    ];
@endphp
<x-layouts.cliente :titulo="__('ponte.conta.titulo')">
    <h1 class="text-2xl font-bold">{{ __('ponte.conta.titulo') }}</h1>

    <div class="mt-6 space-y-6">
        <x-cartao :titulo="__('ponte.conta.seus_dados')">
            <dl class="grid gap-x-8 gap-y-4 sm:grid-cols-2">
                @foreach ($dados as $campo => $valor)
                    <div @class(['sm:col-span-2' => $campo === 'endereco'])>
                        <dt class="text-sm text-mudo">{{ __('ponte.conta.'.$campo) }}</dt>
                        <dd class="mt-0.5 whitespace-pre-line break-words font-medium text-navy">{{ $valor ?: '—' }}</dd>
                    </div>
                @endforeach
            </dl>
            <p class="mt-5 text-sm text-mudo">{{ __('ponte.conta.dados_ajuda') }}</p>
        </x-cartao>

        @if ($cliente?->temContrato())
            <x-cartao :titulo="__('ponte.conta.contrato')">
                <a href="{{ route('conta.contrato') }}" class="inline-flex min-h-11 items-center gap-2 rounded-campo border border-linha bg-white px-4 font-titulo text-[0.95rem] font-semibold text-navy transition hover:border-tec hover:text-tec">
                    <x-icone nome="baixar" class="h-4 w-4" />{{ __('ponte.conta.baixar_contrato') }}
                </a>
            </x-cartao>
        @endif

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
    </div>
</x-layouts.cliente>
