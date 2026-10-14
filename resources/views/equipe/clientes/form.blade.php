@php($novo = ! $cliente->exists)
<x-layouts.equipe
    :titulo="$novo ? 'Novo cliente' : 'Editar cadastro'"
    :subtitulo="$novo ? null : $cliente->empresa"
    :voltar="$novo ? ['url' => route('equipe.clientes.index'), 'nome' => __('equipe.cadastro_titulo')] : ['url' => route('equipe.clientes.show', $cliente), 'nome' => $cliente->empresa]"
>
    <form method="POST" action="{{ $novo ? route('equipe.clientes.store') : route('equipe.clientes.update', $cliente) }}" enctype="multipart/form-data" class="max-w-2xl space-y-6" novalidate>
        @csrf
        @unless ($novo)
            @method('PUT')
        @endunless

        <x-cartao titulo="Empresa e contato">
            <div class="space-y-5">
                <x-campo nome="empresa" rotulo="Empresa" :valor="$cliente->empresa" required autofocus />
                <x-campo nome="contato_nome" rotulo="Pessoa de contato" :valor="$cliente->contato_nome" required />
                <x-campo nome="email" rotulo="E-mail de acesso" tipo="email" :valor="$cliente->email" required />
                <x-campo nome="email_avisos" :rotulo="__('equipe.email_avisos')" tipo="email" :valor="$cliente->email_avisos" :placeholder="__('Igual ao e-mail de acesso')" />
                <x-campo nome="telefone" rotulo="Telefone / WhatsApp" tipo="tel" :valor="$cliente->telefone" inputmode="tel" opcional />
            </div>
        </x-cartao>

        <x-cartao titulo="Informações internas">
            <p class="-mt-2 mb-5 text-sm text-mudo">Guardadas criptografadas. O cliente não vê nada desta parte.</p>
            <div class="space-y-5">
                <x-campo nome="endereco" rotulo="Endereço" :valor="$cliente->endereco" linhas="2" opcional />
                <x-campo nome="observacoes" rotulo="Observações" :valor="$cliente->observacoes" linhas="4" opcional />
                <x-campo nome="contrato" rotulo="Contrato (PDF)" tipo="file" accept="application/pdf" :ajuda="$cliente->temContrato() ? 'Já existe um contrato anexado. Enviar outro substitui o atual.' : 'Até 20 MB.'" opcional />
            </div>
        </x-cartao>

        <div class="flex flex-wrap gap-3">
            <x-botao>{{ $novo ? 'Cadastrar cliente' : 'Salvar alterações' }}</x-botao>
            <x-botao variante="secundario" :href="$novo ? route('equipe.clientes.index') : route('equipe.clientes.show', $cliente)">Cancelar</x-botao>
        </div>
        @if ($novo)
            <p class="text-sm text-mudo">Cadastrar não envia nada ao cliente. O acesso só é enviado quando você clicar em "Enviar acesso".</p>
        @endif
    </form>
</x-layouts.equipe>
