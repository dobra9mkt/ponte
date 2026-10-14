@php
    $usuario = $cliente->usuario;
    $jaEnviado = $usuario?->password !== null || $usuario?->convites()->exists();
    $whatsapp = $cliente->whatsappUrl();
@endphp
<x-layouts.equipe :titulo="$cliente->empresa" :voltar="['url' => route('equipe.clientes.index'), 'nome' => __('equipe.cadastro_titulo')]">
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
                {{ __('fluxo.editar_cadastro') }}
            </x-botao>
        @endcan
    </x-slot:acoes>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
        <div class="space-y-6">
            <section aria-labelledby="servicos-titulo" x-data="alternar">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 id="servicos-titulo" class="text-xl font-semibold">{{ __('equipe.servicos') }}</h2>
                    @can('update', $cliente)
                        <x-botao variante="secundario" type="button" x-on:click="alternar" x-show="!aberto">
                            <x-icone nome="mais" class="h-4 w-4" />{{ __('equipe.cadastrar_servico') }}
                        </x-botao>
                    @endcan
                </div>

                @can('update', $cliente)
                    <form method="POST" action="{{ route('equipe.frentes.store', $cliente) }}" x-data="servicoNovo"
                        @class(['mt-4 rounded-cartao bg-white p-5 shadow-cartao sm:p-6']) @unless ($errors->hasAny(['modelo_id', 'nome', 'inicio_em', 'prazo_final'])) x-show="aberto" x-cloak @endunless>
                        @csrf
                        <div class="grid gap-4 sm:grid-cols-[minmax(0,1fr)_11rem_11rem]">
                            <div class="space-y-1.5">
                                <label for="campo-modelo_id" class="block text-[0.95rem] font-medium text-navy">{{ __('equipe.servico') }}</label>
                                <select id="campo-modelo_id" name="modelo_id" class="campo" x-ref="servico" x-on:change="escolher">
                                    @foreach ($servicos as $servico)
                                        <option value="{{ $servico->id }}" @selected((string) old('modelo_id') === (string) $servico->id)>{{ $servico->nome }}</option>
                                    @endforeach
                                    <option value="" @selected(old('modelo_id', $servicos->isEmpty() ? '' : null) === '')>{{ __('equipe.outro') }}</option>
                                </select>
                            </div>
                            <x-campo nome="inicio_em" tipo="date" :rotulo="__('equipe.inicio')" />
                            <x-campo nome="prazo_final" tipo="date" :rotulo="__('equipe.termino')" />
                        </div>
                        <div class="mt-4" x-show="outro" x-cloak>
                            <x-campo nome="nome" :rotulo="__('equipe.nome_servico')" maxlength="100" />
                        </div>
                        <div class="mt-5 flex flex-wrap gap-2">
                            <x-botao>{{ __('equipe.cadastrar_servico') }}</x-botao>
                            <x-botao variante="discreto" type="button" x-on:click="fechar">{{ __('equipe.cancelar') }}</x-botao>
                        </div>
                    </form>
                @endcan

                <div class="mt-4 space-y-3">
                    @forelse ($cliente->frentes as $frente)
                        @php
                            $atual = App\Models\Frente::etapaAtual($frente->etapas);
                            $prontas = $frente->etapas->filter(fn ($e) => $e->estado->finalizada())->count();
                        @endphp
                        <a href="{{ route('equipe.frentes.ver', [$cliente, $frente]) }}" class="group block rounded-cartao bg-white p-5 shadow-cartao transition hover:ring-1 hover:ring-tec/40">
                            <span class="flex items-start justify-between gap-3">
                                <span class="min-w-0">
                                    <span class="block break-words font-titulo text-[1.05rem] font-semibold text-navy">{{ $frente->nome }}</span>
                                    <span class="mt-1 block text-sm text-mudo">{{ $atual ? __('equipe.agora', ['fase' => $atual->titulo]) : $frente->situacao->rotulo() }}</span>
                                </span>
                                <x-icone nome="seta" class="mt-1 h-5 w-5 text-mudo transition group-hover:text-tec" />
                            </span>
                            <x-progresso :feitas="$prontas" :total="$frente->etapas->count()" class="mt-4" />
                            <span class="mt-2 flex flex-wrap justify-between gap-2 text-xs text-mudo">
                                <span>{{ __('equipe.fases_concluidas', ['feitas' => $prontas, 'total' => $frente->etapas->count()]) }}</span>
                                <span>{{ __('equipe.previsao', ['data' => $frente->prazo_final?->format('d/m/Y') ?? __('equipe.a_combinar')]) }}</span>
                            </span>
                        </a>
                    @empty
                        <p class="rounded-cartao border border-dashed border-linha px-5 py-8 text-center text-sm text-mudo">{{ __('equipe.sem_servicos') }}</p>
                    @endforelse
                </div>
            </section>
            <details class="rounded-cartao border border-linha bg-white">
                <summary class="cursor-pointer px-5 py-4 font-titulo text-sm font-semibold text-navy">{{ __('fluxo.contato') }}</summary>
                <div class="space-y-5 border-t border-linha p-5">
                    <dl class="grid gap-5 sm:grid-cols-2">
                        <x-dado icone="conta" rotulo="Pessoa de contato">{{ $cliente->contato_nome }}</x-dado>
                        <x-dado icone="email" rotulo="E-mail de acesso"><span class="break-all">{{ $cliente->email }}</span></x-dado>
                        <x-dado icone="enviar" rotulo="Avisos de aprovação"><span class="break-all">{{ $cliente->emailDeAvisos() }}</span></x-dado>
                        <x-dado icone="telefone" rotulo="Telefone">
                            {{ $cliente->telefone ?: '—' }}
                            @if ($whatsapp)<a href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer" class="link ml-1 text-sm">WhatsApp ↗</a>@endif
                        </x-dado>
                        <x-dado icone="local" rotulo="Endereço"><span class="whitespace-pre-line">{{ $cliente->endereco ?: '—' }}</span></x-dado>
                    </dl>
                    <div class="border-t border-linha pt-5">
                        <h3 class="text-sm font-semibold">{{ __('fluxo.observacoes') }}</h3>
                        <p class="mt-2 whitespace-pre-line text-sm text-mudo">{{ $cliente->observacoes ?: __('fluxo.sem_observacoes') }}</p>
                    </div>
                </div>
            </details>
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
            @can('delete', $cliente)
                <x-excluir class="px-1" :acao="route('equipe.clientes.destroy', $cliente)" :rotulo="__('equipe.excluir_cliente')"
                    :mensagem="__('equipe.excluir_cliente_confirmar', ['cliente' => $cliente->empresa])" />
            @endcan
        </aside>
    </div>
</x-layouts.equipe>
