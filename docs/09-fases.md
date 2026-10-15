# 09 · Fases de construção

Cinco fases e um fechamento. Cada fase termina com a Rose testando no navegador e aprovando; só então
começa a próxima. As fases 1 a 3 bastam para receber a Bela Bartók.

**Fase atual: Fase 2 · Área da equipe — refeita pela Rose em 14/10/2026.**

Estrutura em [`14-fluxo-equipe.md`](14-fluxo-equipe.md): cadastrar cliente, cadastrar serviço com as
fases do fluxo padrão, fases com responsável e datas, cards de aprovação por rodada, prazo de 5 dias
úteis, lembrete e alerta de vencido, aba Clientes só para consulta. **Foco só no login da equipe;**
o login do cliente vem na fase 3. O trabalho local dos docs 12 e 13 (sem commit) é a base que será
adaptada; nada vai para o git antes de a Rose aprovar.

> **Onde paramos (13/10/2026):** fase 1 aprovada pela Rose e salva. A publicação na Hostinger continua
> pendente: depende da Preparação (GitHub, Hostinger, e-mail, backup).

---

## Antes da fase 1 · Preparação (sem código)

- [ ] Instalar no computador: Laravel Herd (Windows), Node 24, Git
- [x] Criar o repositório privado `ponte` no GitHub (`dobra9mkt/ponte`, 14/10/2026)
- [ ] Hostinger: passos 1 a 4 de [`08-infra-e-deploy.md`](08-infra-e-deploy.md)
- [ ] E-mail: caixa de envio no SMTP da Hostinger
- [ ] Definir o destino das cópias de segurança fora da Hostinger
- [ ] Gerar o `PONTE_CAMINHO_EQUIPE` e guardá-lo no gerenciador de senhas da Rose

---

## Fase 1 · Base segura

**Entrega para a Rose testar:** entrar como equipe com 2FA, cadastrar um cliente, enviar o acesso,
entrar como esse cliente e ver uma tela Início vazia.

Tarefas:
- [x] Criar o projeto Laravel nesta pasta; Pest, Pint, Larastan; Tailwind + Alpine CSP via Vite
- [x] Idioma `pt_BR`, fuso `America/Sao_Paulo`, `Model::shouldBeStrict()` fora de produção
- [x] Layout base com a identidade ([`10-identidade-visual.md`](10-identidade-visual.md)) e componentes Blade (botão, cartão, campo, aviso, contador de revisões)
- [x] Middleware `CabecalhosSeguranca` (SEG-31, SEG-32) e `robots.txt`
- [x] Tabelas `users`, `convites`, `clientes`, `atividades` ([`04-modelo-de-dados.md`](04-modelo-de-dados.md))
- [x] Fortify: login do cliente (C1), "lembrar de mim", esqueci/redefinir senha
- [x] Área da equipe no caminho secreto com 404 (SEG-01), login E1 e 2FA obrigatório E2 (SEG-02)
- [x] Comando `ponte:criar-equipe`
- [x] Clientes: lista E5, ficha E6, formulário com campos criptografados (RN-06, SEG-51) e contrato criptografado (RN-07, SEG-26)
- [x] "Enviar acesso": convite com token em hash, 72 h, uso único (RN-02, RN-03, SEG-08) + e-mail
- [x] Criar senha C2, boas-vindas C3, Início C4 vazio, Minha conta C9, Privacidade
- [x] Limites de tentativas (SEG-05) e alerta de falhas
- [x] Registro de atividades (RN-41) com bloqueio de update/delete (SEG-14)
- [ ] Publicação automática e primeira publicação em `ponte.dobra9.com.br`

Pronto quando:
- [x] Todos os testes de [`11-testes.md`](11-testes.md) da fase 1 passam (82, em 13/10/2026)
- [ ] securityheaders.com dá A+ no portal publicado
- [ ] A Rose testou no celular e aprovou

---

## Fase 2 · Área da equipe (refeita em 14/10/2026)

**Entrega:** a equipe cadastra um cliente, cadastra um serviço com as fases carregadas, organiza as
fases e mostra um card de aprovação ao cliente, com prazo, e-mail, lembrete e alerta de vencido.

Tarefas:
- [x] Barra lateral: Painel, Cadastrar cliente, Clientes, Serviços (RN-80)
- [x] Cadastro do cliente com e-mail de avisos de aprovação (RN-06)
- [x] Cadastrar serviço: lista de serviços e fases do fluxo padrão carregadas (RN-10, RN-11, RN-13)
- [x] Carga inicial dos seis serviços do doc 14 e tela Serviços para criar e editar fluxos padrão (RN-10a)
- [x] Fase com responsável, o que fazer, entrada e término; editar no card (RN-12); situações (RN-14)
- [x] Card de aprovação: rascunho, anexos de qualquer tipo, links, rodadas e histórico (RN-70, RN-71)
- [x] Mostrar para cliente: bloqueio, hash, prazo de 5 dias úteis editável, fase "Aguardando cliente", e-mail (RN-72, RN-73)
- [x] Agendador: lembrete 1 dia útil antes e alerta de vencido à equipe (RN-74, RN-75)
- [x] Nova rodada a partir de "Ajustes solicitados" (RN-76)
- [x] Painel com prazos vencidos, aguardando cliente e ajustes
- [x] Aba Clientes só para consulta, com histórico de aprovações (RN-81)
- [x] Policies e testes de acesso; área da equipe com 404 para quem não é equipe

Pronto quando: a Rose cadastra um cliente de teste, cadastra um serviço, mostra um card, vê o e-mail
no Mailpit, simula a resposta e confere o histórico na aba Clientes, e aprova.

---

## Fase 3 · Login do cliente

**Entrega:** o cliente vê os cards publicados, aprova ou pede ajuste, e a equipe recebe o retorno.

Tarefas (a detalhar quando a fase 2 for aprovada):
- [x] Telas do cliente: linha do tempo, seletor de serviços, card com Aprovar e Solicitar ajustes, Minha conta
- [x] Regras de revisão (RN-20 a RN-29) substituídas pelo modelo de rodadas (14/10/2026)
- [x] E-mails de retorno à equipe
- [ ] WhatsApp (escolha de propostas saiu em 14/10/2026)

---

## Fase 4 · Pendências e arquivos

**Entrega:** o cliente envia materiais e responde o briefing pelo portal; a área de arquivos funciona.

Tarefas:
- [ ] Uploads do cliente (RN-50, SEG-22, SEG-25) com limite de requisições
- [ ] Pendência: enviar, confirmar recebimento, pedir complemento
- [ ] Formulário de briefing a partir de `dados/briefing-rebranding.yaml`, com rascunho automático
- [ ] Tela Arquivos C8 (RN-52)
- [ ] Entrega final e recorrente (relatório mensal)

Pronto quando: a Rose responde o briefing e envia fotos como cliente de teste e aprova.

---

## Fase 5 · Calendário, lembretes e arquivamento

**Entrega:** calendário da equipe, lembretes de prazo, resumo de segunda e o arquivamento completo.

Tarefas:
- [ ] Calendário E4 (mês e semana, cor por cliente)
- [ ] Lembretes RN-31, contagem de dias de espera RN-32, resumo RN-33
- [ ] Concluir serviço (RN-60), aviso de 30 dias (RN-61)
- [ ] Arquivamento E11: pacote ZIP + PDF do histórico em fila, download, confirmação digitando o nome, remoção (RN-62 a RN-65)
- [ ] Página "projeto concluído e arquivado" com link para dobra9.com.br (RN-65a)
- [ ] Cópias de segurança diárias fora da Hostinger (SEG-54)

Pronto quando: a Rose arquiva um serviço de teste, abre o pacote no computador e confere que nada
dele ficou no portal.

---

## Fechamento · antes do primeiro cliente real

- [ ] Roteiro completo do teste de invasão ([`06-seguranca.md`](06-seguranca.md))
- [ ] Restauração de cópia de segurança testada
- [ ] Ensaio completo com cliente fictício, do cadastro ao arquivamento
- [ ] Cadastro real da Bela Bartók e "Enviar acesso"
