@php
    $usuario = $cliente->usuario;
    $jaEnviado = $usuario?->password !== null || $usuario?->convites()->exists();
    $whatsapp = $cliente->whatsappUrl();
@endphp
<x-layouts.equipe :titulo="$cliente->empresa" :voltar="['url' => route('equipe.clientes.index'), 'nome' => 'Clientes']">
    <x-slot:cabecalho>
        <span class="flex items-start gap-3">
            <span class="mt-[0.6em]"><x-cor-cliente :cor="$cliente->cor" /></span>
            <span>
                {{ $cliente->empresa }}
                @unless ($cliente->estaAtivo())
                    <x-etiqueta class="ml-1 align-middle">{{ $cliente->situacao->rotulo() }}</x-etiqueta>
                @endunless
            </span>
        </span>
    </x-slot:cabecalho>
    <x-slot:acoes>
        @can('update', $cliente)
            <x-botao variante="secundario" :href="route('equipe.clientes.edit', $cliente)">
                <x-icone nome="editar" class="h-4 w-4" />
                Editar
            </x-botao>
        @endcan
    </x-slot:acoes>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
        <div class="space-y-6">
            <x-cartao titulo="Contato">
                <dl class="grid gap-5 sm:grid-cols-2">
                    <x-dado icone="conta" rotulo="Pessoa de contato">{{ $cliente->contato_nome }}</x-dado>
                    <x-dado icone="email" rotulo="E-mail de acesso"><span class="break-all">{{ $cliente->email }}</span></x-dado>
                    <x-dado icone="telefone" rotulo="Telefone">
                        {{ $cliente->telefone ?: '—' }}
                        @if ($whatsapp)
                            <a href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer" class="link ml-1 text-sm">WhatsApp ↗</a>
                        @endif
                    </x-dado>
                    <x-dado icone="local" rotulo="Endereço"><span class="whitespace-pre-line">{{ $cliente->endereco ?: '—' }}</span></x-dado>
                </dl>
            </x-cartao>

            <x-cartao titulo="Observações internas">
                <p class="whitespace-pre-line text-[0.95rem] {{ $cliente->observacoes ? 'text-texto' : 'text-mudo' }}">{{ $cliente->observacoes ?: 'Nenhuma observação.' }}</p>
            </x-cartao>

            <x-cartao titulo="Serviços">
                <div class="rounded-campo border border-dashed border-linha px-6 py-8 text-center">
                    <p class="text-sm text-mudo">Os serviços (Rebranding, Site…) aparecem aqui na próxima etapa do portal.</p>
                </div>
            </x-cartao>
        </div>

        <aside class="space-y-6">
            <x-cartao titulo="Acesso ao portal">
                <x-acesso-cliente :cliente="$cliente" />
                @can('enviarAcesso', $cliente)
                    <form method="POST" action="{{ route('equipe.clientes.enviar-acesso', $cliente) }}" class="mt-4">
                        @csrf
                        <x-botao :variante="$jaEnviado ? 'secundario' : 'principal'" largo>
                            <x-icone nome="enviar" class="h-4 w-4" />
                            {{ $jaEnviado ? 'Reenviar acesso' : 'Enviar acesso' }}
                        </x-botao>
                    </form>
                    <p class="mt-2 text-xs text-mudo">O cliente recebe um link para criar a senha, válido por 72 horas.</p>
                @endcan
            </x-cartao>

            <x-cartao titulo="Contrato">
                @can('baixarContrato', $cliente)
                    <a href="{{ route('equipe.clientes.contrato', $cliente) }}" class="flex min-h-11 items-center gap-3 rounded-campo border border-linha px-3 py-2 transition hover:border-tec">
                        <x-icone nome="nota" class="h-5 w-5 text-tec" />
                        <span class="flex-1 text-sm font-medium text-navy">Contrato (PDF)</span>
                        <x-icone nome="baixar" class="h-4 w-4 text-mudo" />
                    </a>
                    <p class="mt-2 text-xs text-mudo">Guardado criptografado. O cliente não vê.</p>
                @else
                    <p class="text-sm text-mudo">Nenhum contrato anexado.</p>
                @endcan
            </x-cartao>

            <details class="group rounded-cartao bg-white shadow-cartao">
                <summary class="flex min-h-12 cursor-pointer list-none items-center justify-between gap-3 px-5 py-3 text-[0.95rem] font-medium text-navy [&::-webkit-details-marker]:hidden">
                    <span>Histórico <span class="font-normal text-mudo">({{ $totalAtividades }})</span></span>
                    <x-icone nome="seta" class="h-4 w-4 text-mudo transition group-open:rotate-90" />
                </summary>
                <div class="border-t border-linha px-5 pb-5 pt-4">
                    <p class="mb-4 text-xs text-mudo">Registro de tudo o que aconteceu com este cliente. Só a equipe vê.</p>
                    <x-atividades :atividades="$atividades" mostrar-ip />
                </div>
            </details>

            @can('alterarSituacao', $cliente)
                <div class="px-1">
                    @if ($cliente->estaAtivo())
                        <div x-data="confirmar">
                            <button type="button" class="text-sm font-medium text-mudo underline-offset-4 hover:text-alerta hover:underline" x-on:click="abrir" x-show="!aberto">Desativar cliente</button>
                            <form method="POST" action="{{ route('equipe.clientes.desativar', $cliente) }}" x-show="aberto" x-cloak class="rounded-cartao border border-alerta/30 bg-white p-4">
                                @csrf
                                <p class="text-sm">O cliente deixa de entrar no portal na hora. Os dados continuam guardados.</p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <x-botao variante="perigo">Desativar</x-botao>
                                    <x-botao variante="secundario" type="button" x-on:click="fechar">Cancelar</x-botao>
                                </div>
                            </form>
                        </div>
                    @else
                        <form method="POST" action="{{ route('equipe.clientes.reativar', $cliente) }}">
                            @csrf
                            <x-botao variante="secundario" largo>Reativar cliente</x-botao>
                        </form>
                    @endif
                </div>
            @endcan
        </aside>
    </div>
</x-layouts.equipe>
