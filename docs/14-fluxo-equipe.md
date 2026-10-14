# Fluxo da equipe · 14/10/2026

Estrutura definida pela Rose em 14/10/2026. Substitui `12-fluxo-simples.md` e `13-aprovacao-simples.md`
no que toca à equipe. **Foco agora só no login da equipe**; o que o cliente vê e responde é
definido depois, na fase do login do cliente.

Nas telas: **serviço** e **fase**. No código: `Frente` e `Etapa`. O card de aprovação é a `Entrega`.

## Visão geral

```
Cadastrar cliente → Cadastrar serviço → Fases (quem, o quê, datas)
      → Adicionar aprovação (card em rascunho) → Mostrar para cliente
      → Prazo de 5 dias úteis → lembrete na véspera / alerta de vencido
      → Retorno do cliente: aprovou (fase segue) ou pediu ajuste (nova rodada)
```

## Barra lateral

| Item | Para quê |
|---|---|
| Painel | O que espera por nós, o que espera o cliente, prazos vencidos |
| Cadastrar cliente | Lista dos clientes em trabalho, botão **Novo cliente** e a ficha de cada um, onde tudo é editado (passos 1 a 7) |
| Clientes | Consulta, **somente visualização** (passo 8) |
| Serviços | Lista de serviços e seus fluxos padrão: criar serviço novo, renomear, editar, reordenar e remover fases |
| Lixeira | Clientes, serviços e fases excluídos, com **Restaurar** (RN-82) |

## 1. Cadastrar cliente

Empresa, pessoa de contato, e-mail de acesso, telefone, endereço, observações, contrato (opcional) e
o **e-mail que recebe os avisos de aprovação** (vem preenchido com o e-mail do contato e pode ser outro).

## 2. Cadastrar serviço (dentro do cliente)

Na ficha do cliente, **Cadastrar serviço**: escolher na lista e, se quiser, a data de início. As fases
do fluxo padrão daquele serviço já vêm carregadas e podem ser editadas, removidas, reordenadas ou
completadas com novas fases. **Outro (escrever o nome)** cria um serviço fora do escopo, sem fases,
para montar na hora. Editar aqui não muda o fluxo padrão nem os serviços de outros clientes.

**Tudo é editável:** na barra lateral, **Serviços** permite criar um serviço novo e mudar o fluxo
padrão de qualquer um. As mudanças valem para os próximos cadastros.

Fluxos padrão iniciais (definidos pela Rose em 14/10/2026):

| Serviço | Fases do fluxo padrão |
|---|---|
| Sistemas sob medida | Levantamento de processos e regras → Desenvolvimento → Integrações e automações → Testes → Aprovação do cliente → Treinamento e documentação → Entrega |
| Aplicativos Android e iPhone | Definição de funcionalidades e telas → Design das telas → Desenvolvimento → Testes → Aprovação do cliente → Publicação na Google Play e App Store → Entrega |
| Criação de sites | Pesquisa → Copy → Preparo das imagens → Desenvolvimento → Aprovação do cliente → Entrega |
| Segurança e remoção de vírus | Diagnóstico → Remoção do vírus e recuperação → Correção da falha → Validação do cliente → Entrega com relatório |
| Servidores web Linux | Levantamento da necessidade → Configuração do servidor → Configuração de backups e monitoramento → Migração dos sites/sistemas → Testes → Validação do cliente → Entrega |
| Tráfego pago (Google e Meta Ads) | Planejamento → Criação dos anúncios → Aprovação do cliente → Configuração das campanhas e conversões → Lançamento |

Nas fases **Aprovação do cliente** e **Validação do cliente** ficam os cards de aprovação, um por
rodada (1ª, 2ª e 3ª revisão). Outras fases também podem receber card, se precisar (RN-70).

## 3. Cada fase

    02 Layout                                   Em andamento
    Quem faz: Rose · Desenhar as telas principais
    Entrada 20/10 · Previsão de término 30/10
    [Adicionar aprovação]                       [Concluir fase]

Campos: nome, **responsável** (texto livre: Rose, Elias, um freelancer que não acessa o portal,
o cliente...), **o que fazer**, **data de entrada** e
**previsão de término**. Edição direto no card, com Salvar e Cancelar.

Situações: **A fazer**, **Em andamento**, **Aguardando cliente**, **Ajustes**, **Concluída**.
"Aguardando cliente" só aparece por um card publicado; não se escolhe à mão.

## 4. Adicionar aprovação (em qualquer fase, só quando precisar)

    1ª revisão · Rascunho
    O que revisar   [Confira o menu e as cores da página inicial]
    Anexos          layout-v1.pdf  [+ Anexar]
    Links           Site de teste · https://...  [+ Link]
    Prazo           23/10 (5 dias úteis)  [alterar]
    [Mostrar para cliente]          Excluir rascunho

- O card nasce em **rascunho**, visível só para a equipe, e pode ser editado ou excluído.
- O que revisar (obrigatório), anexos de **qualquer tipo** (até 50 MB cada) e links (https).
- **Um card por rodada**: 1ª, 2ª, 3ª revisão. Os cards anteriores ficam abaixo, só leitura,
  formando o histórico da fase. Só existe um card aberto por fase de cada vez.

## 5. Mostrar para cliente (botão no card)

- O card é publicado e **a edição é bloqueada** (só o prazo continua alterável).
- **Prazo**: 5 dias úteis a partir da publicação (o rascunho já mostra a data prevista), sem contar fins de semana e feriados
  (`config/feriados.php`). O dia da publicação não conta. Ex.: publicado na sexta 16/10 → vence
  sexta 23/10. A equipe pode mudar a data à mão; a mudança fica no histórico.
- A fase muda para **Aguardando cliente**.
- O e-mail de avisos do cliente recebe o aviso com o prazo e o botão para abrir o portal.

## 6. Acompanhamento do prazo

- **1 dia útil antes do vencimento**: lembrete automático por e-mail ao cliente (uma vez por card).
- **Prazo vencido** sem resposta: alerta para a equipe no Painel e por e-mail (uma vez por card).
  O card mostra "Prazo vencido em 23/10".
- Card respondido não recebe lembrete nem alerta.

## 7. Retorno do cliente (as telas do cliente vêm na próxima fase)

| Cliente | Card | Fase |
|---|---|---|
| Aprovou | "Aprovado em 22/10/2026, às 10:42." Registro imutável | Volta para **Em andamento** e segue; a equipe conclui quando terminar |
| Pediu ajuste | "Ajustes solicitados" com o texto do cliente | **Ajustes**. A equipe faz e clica em **Nova rodada**: cria o card da próxima revisão em rascunho, já com os links da anterior |

## 8. Aba Clientes (somente visualização)

Lista de clientes; ao abrir um, aparecem os serviços, as fases com situação e datas, e o
**histórico de aprovações** (cada card: rodada, publicado em, prazo, resposta e data). Nenhum botão
de edição: para mudar algo, a equipe usa **Cadastrar cliente**.

## Fora desta fase

Telas do cliente (aprovar, pedir ajuste, contador de revisões), escolha de propostas, pendências do
cliente, WhatsApp, calendário, arquivamento. As regras já escritas para elas ficam como estão e serão
revistas quando chegar a vez de cada uma.

## Implementação (14/10/2026)

Rotas da equipe: `/clientes/{cliente}/servicos/{frente}`, `/fases/{etapa}`, `/aprovacoes/{entrega}`,
`/consulta`, `/servicos` (fluxos padrão). Migrações `2026_10_14_000007_fluxo_da_equipe` e
`2026_10_14_000008_carregar_servicos_padrao`. Agendador: `ponte:verificar-prazos` todo dia às 8h.
Anexos da equipe ficam guardados com nome aleatório e extensão neutra (`.bin`) quando o tipo não é conhecido.

## Verificação prevista

- Cadastrar cliente com e-mail de avisos; cadastrar serviço e conferir as fases carregadas.
- Editar fase (responsável, o que fazer, datas) sem alterar o fluxo padrão.
- Card: rascunho editável → publicado bloqueado; prazo de 5 dias úteis com feriado no meio.
- E-mail ao publicar, lembrete na véspera, alerta de vencido (com `Mail::fake()` e data simulada).
- Nova rodada numera 2ª, 3ª revisão e mantém o histórico.
- Aba Clientes sem nenhuma ação de edição; área da equipe com 2FA e 404 para quem não é equipe.
- Rose testa seguindo os passos acima e aprova antes do commit.
