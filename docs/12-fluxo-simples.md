# Fluxo simples · 13/10/2026

> **Substituído em 14/10/2026** pelo fluxo da equipe em `14-fluxo-equipe.md`. Mantido como histórico.

Plano autorizado pela Rose. Implementar localmente e apresentar para teste, sem commit nem push.
Escopo: reorganizar a fase 2. Publicação de entregas, aprovações e uploads permanecem nas fases 3 e 4.

## 1. Ficha do cliente (equipe)

Empresa e contato no cabeçalho. Trabalhos em primeiro plano; acesso, cadastro e histórico secundários.

    Bela Bartók                         Editar cadastro
    Trabalhos                           Adicionar trabalho
    Nova identidade visual             Em preparação
    Site                                Agora: Layout

Adicionar trabalho abre um formulário curto com nome livre e botão Criar trabalho.
Não mostra Rebranding/Site como opções, não cria etapas, não envia convites.

## 2. Organizar um trabalho (equipe)

    Nova identidade visual              Editar trabalho
    Em preparação · só a equipe vê      Mostrar ao cliente
    Etapas                              Adicionar etapa
    01 Conversa inicial   A combinar    Começar etapa
    02 Apresentação       20/11          Editar etapa

Nova etapa exige só título; previsão e descrição são opcionais. Tipo inicial: trabalho da equipe.
Editar abre um formulário com Salvar/Cancelar. Opções de aprovação e revisão ficam recolhidas.
Não há salvamento automático. Campos inválidos mantêm os valores e exibem erros no formulário correto.
Mais opções reúne subir/descer, ocultar e remover com confirmação.
O trabalho pode ser mostrado ao cliente quando tiver uma etapa visível. Isso libera só a linha do tempo.
Novas etapas adicionadas a um trabalho já liberado aparecem no acompanhamento; a tela avisa disso.

## 3. Acompanhamento (cliente)

    Dobra 9 / Ponte                      Minha conta
    Bela Bartók
    Seu projeto, passo a passo.
    Veja como está seu projeto.

    [Se houver algo esperando resposta]
    Precisa de você · Apresentação da marca
    Ver etapa →

    Seus trabalhos
    Nova identidade visual              Ver andamento →
    Agora: Apresentação · previsão 20/11
    1 de 4 etapas concluídas

Sem pendências: mensagem calma, sem botões de ações inexistentes.
Dentro do trabalho: etapa atual em destaque, sequência do trabalho abaixo e revisões discretas.
Sem etapas/datas: mensagem de preparação / A combinar, nunca conclusão ou prazo inventados.
Identidade: navy, azul, teal, Montserrat e Inter; fundo claro, espaço entre blocos, navegação curta.
Celular: uma coluna, ações legíveis, alvos de toque de 44px, sem rolagem lateral.

## Verificação

- Criar trabalho com nome livre, sem modelo nem data; incluir e editar etapas explicitamente.
- Conferir erros de validação, cancelamento, movimentação de datas e etapas recorrentes sem data.
- Rascunhos não aparecem no início nem abrem por URL direta para clientes.
- Mostrar ao cliente exige etapa visível; não envia e-mail; repetições não duplicam atividade.
- Cliente A continua sem acesso aos dados de B; área da equipe continua protegida por 2FA.
- Verificar no navegador em computador e celular; testes, Pint, PHPStan e build.
- Rose revisa e aprova antes de salvar versão no Git.

## Verificação realizada

Em 13/10/2026: 152 testes / 554 verificações, PHPStan nível 6 sem erros, Pint e build aprovados.
Fluxo testado no Chromium com banco separado e dados fictícios: criar trabalho sem datas, adicionar e
editar etapa, manter preparação privada, liberar acompanhamento, iniciar e concluir etapa.
Telas conferidas em 1440px, cliente em 390px e equipe em 360px, incluindo abrir/fechar menu e editar.
Nenhum erro JavaScript/CSP ou rolagem lateral detectado. Migração aplicada no banco local após backup.
Pendente: avaliação e aprovação visual da Rose. Nenhum commit ou push realizado.
