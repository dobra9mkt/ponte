<x-layouts.equipe titulo="Painel" :subtitulo="ucfirst(now()->translatedFormat('l, j \\d\\e F'))">
    <x-slot:cabecalho>{{ $saudacao }}, {{ $primeiroNome }}</x-slot:cabecalho>
    <x-slot:acoes>
        <x-botao :href="route('equipe.clientes.create')">
            <x-icone nome="mais" class="h-4 w-4" />
            Novo cliente
        </x-botao>
    </x-slot:acoes>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-numero rotulo="Clientes ativos" :valor="$clientesAtivos" icone="clientes" />
        <x-numero rotulo="Aguardando criar a senha" :valor="$semSenha" icone="chave" :tom="$semSenha > 0 ? 'atencao' : 'neutro'" />
        <x-numero rotulo="Logins errados em 24 h" :valor="$falhasHoje" icone="escudo" :tom="$falhasHoje > 20 ? 'alerta' : 'neutro'" />
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-cartao class="lg:col-span-2">
            <div class="mb-4 flex items-center justify-between gap-4">
                <h2 class="text-lg font-semibold">Clientes</h2>
                @if ($clientes->isNotEmpty())
                    <a href="{{ route('equipe.clientes.index') }}" class="link text-sm">Ver todos</a>
                @endif
            </div>

            @if ($clientes->isEmpty())
                <div class="rounded-campo border border-dashed border-linha px-6 py-10 text-center">
                    <p class="font-titulo font-semibold text-navy">Nenhum cliente ainda</p>
                    <p class="mt-1 text-sm text-mudo">Cadastre o primeiro. Nada é enviado até você clicar em "Enviar acesso".</p>
                    <x-botao :href="route('equipe.clientes.create')" variante="secundario" class="mt-4">Cadastrar cliente</x-botao>
                </div>
            @else
                <ul class="-mx-2 divide-y divide-linha">
                    @foreach ($clientes as $cliente)
                        <li>
                            <a href="{{ route('equipe.clientes.show', $cliente) }}" class="flex min-h-14 items-center gap-3 rounded-campo px-2 py-3 transition hover:bg-off">
                                <x-cor-cliente :cor="$cliente->cor" />
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate font-medium text-navy">{{ $cliente->empresa }}</span>
                                    <span class="block truncate text-sm text-mudo">{{ $cliente->contato_nome }}</span>
                                </span>
                                <span class="hidden sm:block"><x-acesso-cliente :cliente="$cliente" /></span>
                                <x-icone nome="seta" class="h-4 w-4 text-mudo" />
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-cartao>

        <x-cartao titulo="Aguardando criar a senha">
            @if ($aguardandoSenha->isEmpty())
                <p class="text-sm text-mudo">Todos os clientes já criaram a senha.</p>
            @else
                <ul class="-mx-2 space-y-1">
                    @foreach ($aguardandoSenha as $cliente)
                        <li>
                            <a href="{{ route('equipe.clientes.show', $cliente) }}" class="block rounded-campo px-2 py-2.5 transition hover:bg-off">
                                <span class="block truncate text-[0.95rem] font-medium text-navy">{{ $cliente->empresa }}</span>
                                <x-acesso-cliente :cliente="$cliente" class="mt-1" />
                            </a>
                        </li>
                    @endforeach
                </ul>
                <p class="mt-3 text-xs text-mudo">Abra o cliente para enviar ou reenviar o acesso.</p>
            @endif
        </x-cartao>
    </div>
</x-layouts.equipe>
