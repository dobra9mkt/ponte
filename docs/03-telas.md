# 03 · Telas

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
- C3 (só no 1º acesso): três cartões curtos — **prazo para responder** (em geral 3 dias úteis), **3 revisões por serviço**,
  **o que conta como revisão** — e o botão "Entendi, vamos lá".

### C4 · Início

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

**Escolha** (`escolha`): as opções lado a lado no computador; no celular, abas **"Proposta 1 ·
Proposta 2"** que se alternam com um toque, e cada imagem abre em **tela cheia**. A recomendada tem o
selo **"Recomendada pela Dobra 9"** e o porquê. Botão "Escolher esta"
→ confirmação com a regra de não misturar (RN-28). Também há "Pedir ajuste" se a etapa gastar revisão.

**Pendência** (`pendencia_cliente`): o que enviar (texto da equipe), prazo, área de envio de arquivos
(arrastar ou tocar) + campo de mensagem; ou o **formulário de briefing** quando a etapa for o briefing
(salva rascunho automático; botão "Enviar respostas").

**Trabalho da equipe / entrega final / recorrente**: só leitura, com o aviso da equipe e arquivos.

### C7 · Pedir ajuste (passo 1 → 2)

```
Passo 1                         Passo 2 (confirmação)
│ O que você quer ajustar?   │  │ Você vai usar a revisão  │
│ 1 Onde? [Início ________]  │  │ 2 de 3 do Site.          │
│   [____________________]   │  │ Ela vale para o Site     │
│ 2 Onde? [Contato _______]  │  │ inteiro; depois desta,   │
│   [____________________]   │  │ resta 1.                 │
│ + adicionar item           │  │                          │
│ 📎 Anexar referência       │  │ Confira se incluiu todos │
│ Rascunho salvo às 14:32    │  │ os ajustes.              │
│                            │  │ • Início: …  • Contato: …│
│ [Revisar e enviar →]       │  │ [Voltar] [Usar revisão e │
                                │           enviar]        │
```
- O passo 1 salva sozinho (RN-21a). A cliente pode sair e voltar outro dia; o rascunho continua lá.
- "Onde?" é opcional.
Sem revisões → tela de ajuste extra com o texto de RN-27 e botão "Solicitar ajuste extra".

### C8 · Arquivos
Lista agrupada por serviço: entregas aprovadas e finais, com data e botão Baixar (RN-52).

### C9 · Minha conta
Trocar senha, ver e-mail de acesso, sair de todos os aparelhos, link para a página de privacidade.

---

## Equipe (caminho secreto, com 2FA)

### E1 · Entrar + E2 · Código 2FA
Tela própria, sem logo grande nem nada que identifique "admin". Depois da senha, pede o código de 6
dígitos do aplicativo (ou um código de recuperação). Primeira vez: QR code para configurar.

### E3 · Painel
```
┌───────────────────────────────────────────────────────────────┐
│ Ponte · Equipe       Painel  Calendário  Clientes  Modelos  ⚙ │
├───────────────────┬───────────────────────┬───────────────────┤
│ ESPERA POR NÓS (3)│ ESPERA PELO CLIENTE(2)│ ESTA SEMANA (4)   │
│ Ajustes: Layout   │ Direção visual        │ Seg 09 Propostas  │
│ Bela Bartók · rev2│ Bela Bartók · 2 d.ú.  │ Qua 11 Layout     │
│ Fotos recebidas   │ Fotos e horários      │ …                 │
│ Bela Bartók       │ Bela Bartók · vence   │                   │
│ Orçar ajuste extra│                       │                   │
├───────────────────┴───────────────────────┴───────────────────┤
│ AVISOS: Rebranding Bela Bartók concluído há 30 dias → Arquivar │
└───────────────────────────────────────────────────────────────┘
```

### E4 · Calendário
Mês e semana; cada item = etapa com data prevista ou prazo do cliente; uma cor por cliente; clicar abre
a etapa. Filtro por cliente. (Sem arrastar no calendário na v1: datas mudam na tela da frente.)

### E5 · Clientes (lista) e E6 · Cliente (ficha)
- Lista: empresa, contato, serviços ativos, situação do acesso (não enviado / enviado em DD/MM / ativo).
- Ficha: dados do cadastro (RN-06), contrato anexado (RN-07, só equipe), botão **"Enviar acesso"** /
  "Reenviar acesso", serviços do cliente, botão **"Adicionar serviço"**, desativar, arquivar cliente.
- Formulário novo/editar cliente: empresa*, contato*, e-mail*, telefone, endereço, observações, contrato (PDF).

### E7 · Adicionar serviço
Modelo (Rebranding, Site, … ou Em branco), nome (preenchido pelo modelo), data de início, revisões
incluídas (3), prazo final. Prévia das etapas com as datas calculadas (RN-13) antes de salvar.

### E8 · Serviço (frente) — visão da equipe
Lista de etapas com arrastar para reordenar, editar (título, tipo, descrição para o cliente, data,
gasta revisão?), adicionar, ocultar/remover (RN-12). Contador de revisões, com a lista de pedidos e o
botão "Marcar como correção nossa" em cada um. Botão "Concluir serviço" quando tudo estiver pronto.

### E9 · Etapa — visão da equipe
Conforme o estado: "Iniciar", **"Publicar entrega"** (arquivos + link + nota curta → "Enviar para
aprovação"), opções da escolha (marcar recomendada + por quê), pedidos de ajuste recebidos com os itens,
materiais recebidos ("Confirmar recebimento" / "Pedir complemento"), histórico completo da etapa.
Depois de publicar ou liberar: botão **"Avisar no WhatsApp"** com a mensagem pronta (RN-34).

### E10 · Modelos
Lista de modelos; editar etapas padrão (título, tipo, duração em dias úteis, gasta revisão, descrição
para o cliente) e as revisões incluídas. Mudanças valem só para serviços novos (RN-11).

### E11 · Arquivamento
Lista de serviços concluídos há 30+ dias. Fluxo de RN-62/RN-63: gerar pacote → baixar → confirmar
digitando o nome do cliente → remover.

### E12 · Conta da equipe
Trocar senha, reconfigurar 2FA, ver códigos de recuperação, ver últimos acessos à própria conta.
