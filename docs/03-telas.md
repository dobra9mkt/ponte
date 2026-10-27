# 03 · Telas

**Atualização de 14/10/2026:** as telas da equipe seguem [`14-fluxo-equipe.md`](14-fluxo-equipe.md)
(barra lateral com Painel, Cadastrar cliente e Clientes; serviço com fases do fluxo padrão; cards de
aprovação por rodada). As telas do cliente (C1 a C9) serão revistas na fase do login do cliente; os
esboços abaixo são referência.

Todas pensadas **primeiro para o celular** (360 px) e depois para o computador. Visual em
[`10-identidade-visual.md`](10-identidade-visual.md); textos em [`07-textos-e-emails.md`](07-textos-e-emails.md).
Rotas em [`05-rotas-e-permissoes.md`](05-rotas-e-permissoes.md).

---

## Cliente

### C1 · Entrar

```
┌──────────────────────────────┐
│        [logo Dobra 9]        │
│                              │
│  E-mail     [______________] │
│  Senha      [______________] │
│  [x] Lembrar de mim          │
│                              │
│  [        Entrar          ]  │
│  Esqueci minha senha         │
└──────────────────────────────┘
```
- Erro sempre genérico: "E-mail ou senha incorretos." (nunca dizer qual dos dois).
- Após 5 tentativas: "Muitas tentativas. Tente de novo em 1 minuto."
- Sem link para cadastro (só por convite).

### C2 · Criar senha (pelo convite) e C3 · Boas-vindas

- C2: mostra o nome da empresa, campo senha + confirmar, regras da senha visíveis (mín. 10 caracteres).
- Convite vencido/usado: "Este link não vale mais. Peça um novo à Dobra 9." + WhatsApp.
- C3 (só no 1º acesso): saudação com o primeiro nome do contato e um parágrafo de abertura; três cartões
  com ícone — **quando pedirmos a sua aprovação** (5 dias úteis), **até 3 rodadas de ajustes por
  aprovação**, **acompanhe tudo por aqui** (com o botão do WhatsApp da Dobra 9, se configurado) — e o
  botão "Entendi, vamos começar". Texto escrito para servir também a quem só acompanha, sem aprovação.

### C4 · Início

Substituída pela linha do tempo com abas de serviço (RN-16b). Só aparece sem nenhum serviço mostrado:
boas-vindas e aviso de preparação.

```
┌──────────────────────────────┐
│ [logo]  Bela Bartók      [≡] │
├──────────────────────────────┤
│ ▌PRECISA DE VOCÊ (2)         │
│ ▌● Aprovar: Direção visual   │
│ ▌  Rebranding · há 1 dia útil│
│ ▌● Enviar: Fotos e horários  │
│ ▌  Site · vence 13/11        │
├──────────────────────────────┤
│ REBRANDING                   │
│ ▓▓▓▓▓▓░░░░  etapa 3 de 5     │
│ Revisões  ● ○ ○  1 de 3 usada│
│ Agora: Duas propostas        │
│                      Abrir → │
├──────────────────────────────┤
│ SITE                         │
│ ▓▓░░░░░░░░  etapa 2 de 9     │
│ Revisões  ○ ○ ○  nenhuma usada│
│ Agora: Perfil do Google      │
│                      Abrir → │
└──────────────────────────────┘
Menu: Início · Arquivos · Minha conta · Sair
```
- A faixa "Precisa de você" some quando vazia e aparece "Tudo em dia! A Dobra 9 está trabalhando."
- Frentes concluídas aparecem no fim, recolhidas.

### C5 · Serviço (frente)

```
┌──────────────────────────────┐
│ ← Início                     │
│ REBRANDING                   │
│ ┌──────────────────────────┐ │
│ │ Revisões  ● ○ ○          │ │
│ │ 1 de 3 usadas            │ │
│ └──────────────────────────┘ │
│ Previsão de conclusão: 04/12 │
│                              │
│ ✓ Briefing da marca   06/11  │
│ ✓ Direção visual      12/11  │
│ ● Duas propostas      19/11  │  ← atual, destacada
│   Precisa de você            │
│ ○ Ajustes na escolhida 27/11 │
│ ○ Manual e arquivos   04/12  │
└──────────────────────────────┘
```

**Texto da fase recolhido (27/10/2026):** na linha do tempo do cliente (C5) e na tela do serviço da
equipe (E8), o "O que fazer" de cada fase mostra só as 2 primeiras linhas. Texto longo (mais de 180
caracteres ou mais de 2 linhas) ganha **Ver mais**, que abre o texto inteiro no próprio card, e **Ver
menos**, que fecha. Texto curto aparece normal, sem botão.

**Atualização de 14/10/2026 (RN-17):** a tela do serviço do cliente é uma linha do tempo vertical:

```
Criação de sites                     Previsão de conclusão: 11/11/2026
● Começamos em 05/10/2026
✓ Pesquisa              Concluída        05/10 → 09/10
✓ Copy                  Concluída        09/10 → 16/10
● Preparo das imagens   Aguardando sua aprovação   13/10 → 16/10
   ├ 1ª revisão · enviada em 14/10 · responder até 21/10   [Revisar]
○ Desenvolvimento       Próxima          16/10 → 30/10
```

### C6 · Etapa — varia pelo tipo

**Aprovação** (`aprovacao`, estado `aguardando_cliente`):
```
│ Layout · versão 2            │
│ Enviado em 09/12 por Rose    │
│ "Ajustamos o menu e as cores"│
│ [prévia da imagem / PDF]     │
│ [Abrir link de teste ↗]      │
│ [📎 layout-v2.pdf  Baixar]   │
│                              │
│ Revisões do Site: 1 de 3     │
│ [   Aprovar   ] [Pedir ajuste]│
│ Histórico de versões ▾       │
```
- **Aprovar** abre confirmação: "Aprovar o Layout (versão 2)? Depois de aprovado, a etapa não volta."
- Se a etapa tiver confirmação (RN-43), ela aparece como caixa de marcar acima do botão, que só fica
  ativo depois de marcada.
- Com rascunho de ajuste salvo (RN-21a), o botão vira **"Continuar ajustes (N itens)"**.

**Pendência** (`pendencia_cliente`): o que enviar (texto da equipe), prazo, área de envio de arquivos
(arrastar ou tocar) + campo de mensagem; ou o **formulário de briefing** quando a etapa for o briefing
(salva rascunho automático; botão "Enviar respostas").

**Trabalho da equipe / entrega final / recorrente**: só leitura, com o aviso da equipe e arquivos.

### C7 · Pedir ajuste

No próprio card: campo de texto e imagens opcionais, com rascunho salvo sozinho (RN-21a), e o botão
"Enviar ajustes". Não aparece na versão final (RN-71).

### C8 · Arquivos
Lista agrupada por serviço: entregas aprovadas e finais, com data e botão Baixar (RN-52).

### C9 · Minha conta
Seus dados só para leitura (contato, empresa, e-mail, telefone, endereço), **Baixar contrato (PDF)** quando
houver, e trocar senha. Correções nos dados são feitas pela equipe. Sair fica só no topo.

---

## Equipe (caminho secreto, com 2FA)

### E1 · Entrar + E2 · Código 2FA
Tela própria, sem logo grande nem nada que identifique "admin". Depois da senha, pede o código de 6
dígitos do aplicativo (ou um código de recuperação). Primeira vez: QR code para configurar.
Abaixo do botão Entrar, link discreto "Esqueci minha senha" (RN-05a), com as mesmas telas de pedir
link e criar senha nova do cliente, no layout discreto da equipe.

### Estrutura da área da equipe

Barra lateral fixa à esquerda (no celular, menu que desliza): **Painel**, **Cadastrar cliente**,
**Clientes**, **Serviços** (RN-80) e, no rodapé, a conta da pessoa e Sair. Calendário e Arquivamento entram quando
as fases deles estiverem prontas. Cada tela tem um cabeçalho com título, link de voltar e as ações
principais à direita. Telas de detalhe usam duas colunas: conteúdo no centro e painel lateral.

### E3 · Painel
```
┌──────────────────┬────────────────────────────────────────────────┐
│ Ponte            │ Bom dia, Rose · quarta, 14 de outubro          │
│                  ├────────────────┬────────────────┬──────────────┤
│ Painel           │ PRAZO VENCIDO  │ AGUARDANDO     │ ESPERA POR   │
│ Cadastrar cliente│ Layout · Bela  │ CLIENTE        │ NÓS (ajustes)│
│ Clientes         │ venceu 23/10   │ Logo · Bela    │ Site · Outro │
│                  │                │ vence 27/10    │ 2ª revisão   │
│ ──────────────── │                │                │              │
│ Rose · Sair      │                │                │              │
└──────────────────┴────────────────┴────────────────┴──────────────┘
```
O alerta de prazo vencido (RN-75) aparece aqui até o cliente responder ou a equipe mudar o prazo.
O histórico de atividades (RN-41) não fica no painel.

### E5 · Cadastrar cliente (lista) e E6 · Ficha do cliente
- Lista dos clientes em trabalho e o botão **Novo cliente**.
- Formulário: empresa*, contato*, e-mail de acesso*, **e-mail de avisos de aprovação*** (vem com o
  e-mail do contato), telefone, endereço, observações, contrato (PDF).
- Ficha: cabeçalho com empresa e contato; **serviços** em primeiro plano com o botão
  **Cadastrar serviço**; no painel lateral, cadastro, acesso ("Enviar acesso") e histórico recolhido.

### E7 · Cadastrar serviço
Formulário curto na ficha: escolher o serviço (lista da tela Serviços, ou **Outro** com o nome
escrito) e a data de início (opcional) → **Cadastrar serviço** abre o E8 já com as fases do fluxo padrão (RN-10, RN-13).

### E8 · Serviço — fases
```
Site · Bela Bartók                    Previsão de conclusão: 18/12
──────────────────────────────────────────────────────────────────
01 Mapa do site            Concluída     Elias · 13/10 → 17/10
02 Layout                  Aguardando cliente · prazo 23/10
   Quem faz: Rose · Desenhar as telas principais
   Entrada 20/10 · Término 30/10                     [Editar fase]
   ┌ 1ª revisão · Aguardando o cliente · prazo 23/10 [alterar] ┐
   │ Confira o menu e as cores da página inicial               │
   │ layout-v1.pdf · Site de teste ↗                           │
   └───────────────────────────────────────────────────────────┘
03 Programação             A fazer       Elias · 02/11 → 27/11
+ Nova fase
```
- Cada fase é um card: nome, responsável, o que fazer, entrada, término e situação (RN-12, RN-14).
  **Editar fase** abre os campos no próprio card, com Salvar e Cancelar.
- Um botão de próximo passo por fase (Iniciar fase → Concluir fase) e **Adicionar aprovação**.
  No computador, a ordem das fases muda **arrastando** a alça ⠿ à esquerda do card; a ordem nova é
  salva sozinha. **Mais opções** (⋯) tem Reabrir fase / Voltar para A fazer (RN-14a), ocultar e
  remover; no celular, onde arrastar é difícil, também Subir e Descer.

### E9 · Card de aprovação (dentro da fase)
- **Rascunho**: o que revisar, anexos (qualquer arquivo, RN-50), links, prazo calculado (5 dias úteis,
  com "alterar"), botões **Mostrar para cliente** e "Excluir rascunho" (RN-70, RN-73).
- **Aguardando o cliente**: conteúdo só leitura, prazo com "alterar"; "Prazo vencido em DD/MM" quando
  vencer (RN-72, RN-75).
- **Aprovado**: "Aprovado em 22/10/2026, às 10:42." (RN-76).
- **Ajustes solicitados**: texto do cliente e botão **Nova rodada** (RN-76).
- Rodadas anteriores ficam recolhidas abaixo ("1ª revisão · Ajustes solicitados em 21/10 ▸") (RN-71).

### E10 · Serviços (fluxos padrão)
Lista de serviços com **Novo serviço**. Ao abrir um: nome e as fases do fluxo padrão, cada uma com
nome, responsável, o que fazer e duração em dias úteis (opcional), editáveis no card com Salvar e
Cancelar; subir, descer, adicionar e remover. Desativar tira o serviço da lista de cadastro sem afetar
quem já tem. Mudanças valem só para serviços cadastrados depois (RN-10a, RN-11).

### E13 · Clientes (somente visualização)
Lista de clientes → ao abrir um, cada serviço com resumo (fase atual, barra de progresso, início e término
previsto) e as fases com marcador de situação, responsável e período; as aprovações aparecem dentro da
própria fase (rodada, situação e data). Nenhum botão de edição (RN-81).

### E4 · Calendário
Fica para uma fase seguinte: mês e semana, cada item uma fase ou um prazo de card, cor por cliente.

### E11 · Arquivamento
Lista de serviços concluídos há 30+ dias. Fluxo de RN-62/RN-63: gerar pacote → baixar → confirmar
digitando o nome do cliente → remover.

### E12 · Conta da equipe
Trocar senha, reconfigurar 2FA, ver códigos de recuperação, ver últimos acessos à própria conta.
