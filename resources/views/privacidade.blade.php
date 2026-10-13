<x-layouts.base titulo="Privacidade">
    <main class="mx-auto max-w-[720px] px-4 py-10">
        <img src="{{ asset('marca/dobra9-logo-completo.svg') }}" alt="Dobra 9" class="mb-8 h-8 w-auto">
        <h1 class="text-2xl font-bold">Privacidade no acompanhamento de projetos</h1>

        <div class="mt-6 space-y-6">
            <x-cartao titulo="O que guardamos">
                <p>O nome da empresa, o nome e o e-mail da pessoa de contato, telefone e endereço (guardados de forma criptografada), os arquivos e mensagens trocados no projeto e o registro das aprovações. Também registramos quando você entra no portal e as ações que faz nele (aprovar, pedir ajuste, baixar arquivos), com data, hora, endereço de internet (IP) e tipo de navegador. Isso serve como comprovante das aprovações e para proteger a sua conta contra acessos indevidos. A sua senha nunca é guardada: só uma versão embaralhada que não pode ser revertida.</p>
            </x-cartao>
            <x-cartao titulo="Para quê">
                <p>Só para conduzir o seu projeto: mostrar o andamento, receber materiais, registrar aprovações e avisar você por e-mail quando algo precisar da sua resposta. Não vendemos nem compartilhamos esses dados.</p>
            </x-cartao>
            <x-cartao titulo="Por quanto tempo">
                <p>Enquanto o projeto estiver ativo e por até 30 dias depois de concluído. Depois disso, a Dobra 9 guarda uma cópia fora do portal e apaga os dados do servidor. Cópias de segurança expiram em até 30 dias.</p>
            </x-cartao>
            <x-cartao titulo="Como pedir a remoção">
                <p>Escreva para <a href="mailto:contato@dobra9.com.br" class="link">contato@dobra9.com.br</a> pedindo a remoção dos seus dados. Respondemos em até 15 dias.</p>
            </x-cartao>
        </div>

        <p class="mt-8 text-center">
            <a href="{{ route('inicio') }}" class="link">Voltar</a>
        </p>
    </main>
</x-layouts.base>
