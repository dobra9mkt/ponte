<?php

return [

    'login' => [
        'titulo' => 'Acompanhe o seu projeto',
        'erro' => 'E-mail ou senha incorretos.',
        'bloqueado' => 'Muitas tentativas. Tente de novo em :segundos segundos.',
        'lembrar' => 'Lembrar de mim',
        'entrar' => 'Entrar',
        'esqueci' => 'Esqueci minha senha',
    ],

    'equipe' => [
        'entrar' => 'Entrar',
        'codigo_titulo' => 'Código de verificação',
        'codigo_texto' => 'Digite o código de 6 dígitos do aplicativo autenticador.',
        'codigo_erro' => 'Código inválido.',
        'usar_recuperacao' => 'Usar um código de recuperação',
        'configurar_titulo' => 'Proteja sua conta',
        'configurar_texto' => 'Abra o aplicativo autenticador (Google Authenticator, Microsoft Authenticator ou 1Password), leia o QR code e digite o código que aparecer.',
        'chave_manual' => 'Sem câmera? Digite esta chave no aplicativo:',
        'recuperacao_titulo' => 'Guarde os códigos de recuperação',
        'recuperacao_texto' => 'Cada código funciona uma vez, se você perder o celular. Guarde-os no gerenciador de senhas. Eles não aparecem de novo sem a sua senha.',
    ],

    'convite' => [
        'titulo' => 'Crie sua senha',
        'texto' => 'Este é o espaço da :empresa para acompanhar o projeto com a Dobra 9.',
        'invalido' => 'Este link não vale mais. Peça um novo à Dobra 9.',
        'botao' => 'Criar senha e entrar',
        'whatsapp' => 'Falar com a Dobra 9 no WhatsApp',
    ],

    'senha' => [
        'regra' => 'Use pelo menos 10 caracteres. Evite senhas que você já usa em outros sites.',
        'email_enviado' => 'Se este e-mail estiver cadastrado, você vai receber o link em alguns minutos.',
        'pedir_titulo' => 'Esqueci minha senha',
        'pedir_texto' => 'Informe o e-mail de acesso. Enviaremos um link para você criar uma senha nova.',
        'pedir_botao' => 'Enviar link',
        'redefinir_titulo' => 'Nova senha',
        'redefinir_botao' => 'Salvar nova senha',
        'redefinida' => 'Senha alterada. Entre com a senha nova.',
        'link_invalido' => 'Este link não vale mais. Peça um novo em "Esqueci minha senha".',
        'trocada' => 'Senha alterada.',
    ],

    'boas_vindas' => [
        'titulo' => 'Boas-vindas ao acompanhamento do seu projeto',
        'cartoes' => [
            ['titulo' => 'Responda dentro do prazo.', 'texto' => 'Em geral são 3 dias úteis; o prazo de cada entrega aparece na tela. O cronograma anda junto com a sua resposta.'],
            ['titulo' => 'Você tem 3 revisões por serviço.', 'texto' => 'O contador fica sempre visível. Depois da 3ª, ajustes são orçados à parte.'],
            ['titulo' => 'Uma revisão é um pacote.', 'texto' => 'Junte todos os ajustes e envie de uma vez. Erros nossos não contam como revisão.'],
        ],
        'botao' => 'Entendi, vamos lá',
    ],

    'inicio' => [
        'precisa_de_voce' => 'Precisa de você',
        'tudo_em_dia' => 'Tudo em dia! A Dobra 9 está trabalhando no seu projeto.',
        'preparando' => 'Estamos preparando as etapas do seu projeto. Assim que estiverem prontas, elas aparecem aqui.',
    ],

    'conta' => [
        'titulo' => 'Minha conta',
        'email_acesso' => 'E-mail de acesso',
        'trocar_senha' => 'Trocar senha',
        'sair_todos' => 'Sair de todos os aparelhos',
        'sair_todos_texto' => 'Encerra o acesso em todos os outros celulares e computadores. Este aparelho continua conectado.',
        'sair_todos_feito' => 'Pronto. Os outros aparelhos foram desconectados.',
    ],

    'revisoes' => [
        'contador' => ':usadas de :total revisões usadas',
        'nenhuma' => 'Nenhuma revisão usada',
    ],

    'ajuste' => [
        'confirmar' => 'Você vai usar a revisão :n de :total do :frente. Ela vale para o :frente inteiro; depois desta, restam :restantes. Confira se incluiu todos os ajustes: o que chegar depois conta como uma nova revisão.',
        'ultima' => 'Esta é a sua última revisão incluída no :frente. Ajustes depois dela serão orçados à parte.',
        'esgotadas' => 'As :total revisões do :frente já foram usadas. Você pode solicitar um ajuste extra: a Dobra 9 envia o orçamento antes de começar.',
        'extra_enviado' => 'Pedido de ajuste extra enviado. A Dobra 9 vai te mandar o orçamento; o trabalho começa depois do acerto.',
        'enviado' => 'Ajustes enviados. Avisaremos quando a nova versão estiver pronta.',
        'devolvida' => 'Este ajuste era uma correção nossa e não contou como revisão. Você continua com :restantes de :total disponíveis.',
        'rascunho_salvo' => 'Rascunho salvo às :hora. Você pode sair e continuar depois: nada é enviado até você confirmar.',
        'continuar' => 'Continuar ajustes (:qtd itens)',
    ],

    'escolha' => [
        'regra' => 'Escolha uma proposta como base. Elementos das outras podem ser pedidos na revisão, mas não misturamos propostas inteiras.',
        'recomendada' => 'Recomendada pela Dobra 9',
    ],

    'aprovar' => [
        'confirmar' => 'Aprovar :etapa (versão :versao)? Depois de aprovado, a etapa não volta.',
        'feito' => 'Aprovado em :data às :hora por :nome. Obrigado! Seguimos para a próxima etapa.',
    ],

    'esperando' => 'Aguardando a sua resposta há :dias dias úteis. O cronograma anda junto com a sua resposta.',

    'material' => [
        'recebido' => 'Recebemos, obrigado! Vamos conferir e seguir.',
        'complemento' => 'A Dobra 9 pediu um complemento: :mensagem',
    ],

    'clientes' => [
        'salvo' => 'Cliente salvo.',
        'acesso_enviado' => 'Acesso enviado para :email. O link vale por 72 horas.',
        'desativado' => 'Cliente desativado. O acesso foi encerrado.',
        'reativado' => 'Cliente reativado.',
    ],

    'emails' => [
        'rodape' => 'Você recebeu este e-mail porque tem um projeto com a Dobra 9.',
        'convite' => [
            'assunto' => 'Seu acesso ao acompanhamento do projeto · Dobra 9',
            'titulo' => 'Olá, :contato!',
            'texto' => 'Preparamos um espaço para você acompanhar o projeto da :empresa, aprovar as entregas e enviar o que precisarmos. Crie sua senha pelo botão abaixo; o link vale por 72 horas.',
            'botao' => 'Criar minha senha',
        ],
        'senha' => [
            'assunto' => 'Redefinir sua senha · Dobra 9',
            'texto' => 'Recebemos um pedido para redefinir sua senha. Se não foi você, ignore este e-mail.',
            'botao' => 'Redefinir senha',
        ],
        'alerta_login' => [
            'assunto' => '⚠ Tentativas de login suspeitas',
            'texto' => ':qtd tentativas falhas na última hora (IPs: :ips).',
            'botao' => 'Ver registro',
        ],
    ],

];
