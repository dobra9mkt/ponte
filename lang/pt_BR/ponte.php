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
        'codigo_bloqueado' => 'Muitas tentativas com o código. Tente de novo em :minutos min.',
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
        'fazer_agora' => 'O que fazer agora',
        'nada_agora' => 'Você não tem nenhuma pendência agora.',
        'seus_servicos' => 'Seus serviços',
    ],

    'conta' => [
        'titulo' => 'Minha conta',
        'trocar_senha' => 'Trocar senha',
        'seus_dados' => 'Seus dados',
        'contato' => 'Contato',
        'empresa' => 'Empresa',
        'email' => 'E-mail',
        'telefone' => 'Telefone',
        'endereco' => 'Endereço',
        'dados_ajuda' => 'Precisa corrigir algum dado? Fale com a Dobra 9.',
        'contrato' => 'Contrato',
        'baixar_contrato' => 'Baixar contrato (PDF)',
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

    'frentes' => [
        'criada' => 'Trabalho criado. Agora, adicione a primeira etapa.',
        'salva' => 'Trabalho salvo.',
        'previsao' => 'Previsão de conclusão: :data',
        'etapa_de' => 'Etapa :n de :total',
        'agora' => 'Agora: :etapa',
        'concluido' => 'Etapas concluídas',
        'sem_etapas' => 'As etapas deste trabalho ainda estão sendo preparadas.',
    ],

    'modelos' => [
        'salvo' => 'Modelo salvo. As mudanças valem só para serviços adicionados daqui em diante.',
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
        'senha_redefinida' => [
            'assunto' => 'Sua senha foi trocada · Dobra 9',
            'texto' => 'A senha da sua conta no Ponte foi trocada pelo "Esqueci minha senha" e todas as sessões foram encerradas. Se não foi você, avise a equipe agora e troque a senha do seu e-mail.',
            'botao' => 'Entrar',
        ],
        'alerta_login' => [
            'assunto' => '⚠ Tentativas de login suspeitas',
            'texto' => ':qtd tentativas falhas na última hora (IPs: :ips).',
            'botao' => 'Ver registro',
        ],
    ],

];
