# 09 · Fases de construção

Cinco fases e um fechamento. Cada fase termina com a Rose testando no navegador e aprovando; só então
começa a próxima. As fases 1 a 3 bastam para receber a Bela Bartók.

**Fase atual: nenhuma (aguardando autorização da Rose).**

---

## Antes da fase 1 · Preparação (sem código)

- [ ] Instalar no computador: Laravel Herd (Windows), Node 24, Git
- [ ] Criar o repositório privado `ponte` no GitHub (a Rose cria ou autoriza)
- [ ] Hostinger: passos 1 a 4 de [`08-infra-e-deploy.md`](08-infra-e-deploy.md)
- [ ] Resend: chave de API nova só para o Ponte
- [ ] Definir o destino das cópias de segurança fora da Hostinger
- [ ] Gerar o `PONTE_CAMINHO_EQUIPE` e guardá-lo no gerenciador de senhas da Rose

---

## Fase 1 · Base segura

**Entrega para a Rose testar:** entrar como equipe com 2FA, cadastrar um cliente, enviar o acesso,
entrar como esse cliente e ver uma tela Início vazia.

Tarefas:
- [ ] Criar o projeto Laravel nesta pasta; Pest, Pint, Larastan; Tailwind + Alpine CSP via Vite
- [ ] Idioma `pt_BR`, fuso `America/Sao_Paulo`, `Model::shouldBeStrict()` fora de produção
- [ ] Layout base com a identidade ([`10-identidade-visual.md`](10-identidade-visual.md)) e componentes Blade (botão, cartão, campo, aviso, contador de revisões)
- [ ] Middleware `CabecalhosSeguranca` (SEG-31, SEG-32) e `robots.txt`
- [ ] Tabelas `users`, `convites`, `clientes`, `atividades` ([`04-modelo-de-dados.md`](04-modelo-de-dados.md))
- [ ] Fortify: login do cliente (C1), "lembrar de mim", esqueci/redefinir senha
- [ ] Área da equipe no caminho secreto com 404 (SEG-01), login E1 e 2FA obrigatório E2 (SEG-02)
- [ ] Comando `ponte:criar-equipe`
- [ ] Clientes: lista E5, ficha E6, formulário com campos criptografados (RN-06, SEG-51) e contrato criptografado (RN-07, SEG-26)
- [ ] "Enviar acesso": convite com token em hash, 72 h, uso único (RN-02, RN-03, SEG-08) + e-mail
- [ ] Criar senha C2, boas-vindas C3, Início C4 vazio, Minha conta C9, Privacidade
- [ ] Limites de tentativas (SEG-05) e alerta de falhas
- [ ] Registro de atividades (RN-41) com bloqueio de update/delete (SEG-14)
- [ ] Publicação automática e primeira publicação em `ponte.dobra9.com.br`

Pronto quando:
- [ ] Todos os testes de [`11-testes.md`](11-testes.md) da fase 1 passam
- [ ] securityheaders.com dá A+ no portal publicado
- [ ] A Rose testou no celular e aprovou

---

## Fase 2 · Serviços e modelos

**Entrega:** a equipe adiciona Rebranding e Site a um cliente, ajusta as etapas, e o cliente vê a
linha do tempo de cada serviço.

Tarefas:
- [ ] Tabelas `modelos`, `modelo_etapas`, `frentes`, `etapas`; enums `TipoEtapa`, `EstadoEtapa`
- [ ] Seeder lendo `dados/modelos/*.yaml`
- [ ] Serviço `DiasUteis` com `config/feriados.php` (nacionais 2026–2027 + Belo Horizonte)
- [ ] "Adicionar serviço" E7 com prévia das datas (RN-10, RN-11, RN-13)
- [ ] Tela do serviço da equipe E8: reordenar, editar, adicionar, ocultar etapas (RN-12)
- [ ] Modelos E10
- [ ] Cliente: Início C4 com cartões das frentes e contador de revisões (RN-25); Serviço C5; Etapa C6 só leitura
- [ ] Ações `IniciarEtapa`; trabalho da equipe com aviso ao concluir
- [ ] Policies `FrentePolicy`, `EtapaPolicy@view` + testes de acesso cruzado

Pronto quando: a Rose monta o projeto da Bela Bartók (dados de `dados/piloto-bela-bartok.yaml`) com
um cliente de teste e aprova o que o cliente vê.

---

## Fase 3 · Aprovações e revisões

**Entrega:** o ciclo completo publicar → aprovar / pedir ajuste → nova versão → aprovar, com o
contador de revisões, a escolha de propostas e o ajuste extra.

Tarefas:
- [ ] Tabelas `entregas`, `opcoes`, `pedidos_ajuste`, `aprovacoes`, `arquivos`
- [ ] Uploads da equipe no disco privado (SEG-20 a SEG-25) e download com policy
- [ ] Ações `PublicarEntrega`, `AprovarEntrega`, `EscolherOpcao`, `PedirAjuste`, `SolicitarAjusteExtra`, `IniciarAjusteExtra`, `CancelarAjusteExtra`, `MarcarCorrecaoNossa` ([`02-fluxos.md`](02-fluxos.md))
- [ ] Contagem de revisões em transação com trava (RN-29)
- [ ] Hash da aprovação (RN-40)
- [ ] Telas: C6 aprovação e escolha, C7 pedir ajuste em 2 passos, E9 etapa da equipe
- [ ] Todos os textos de [`07-textos-e-emails.md`](07-textos-e-emails.md) desta fase
- [ ] E-mails: entrega publicada, aprovado, ajuste pedido, ajuste extra, revisão devolvida (fila `database`)
- [ ] Painel da equipe E3 (as três listas)

Pronto quando: a Rose faz o Rebranding inteiro de mentira (direção visual, 2 propostas, 3 revisões,
1 correção nossa, 1 ajuste extra) e aprova.

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
- [ ] Cópias de segurança diárias fora da Hostinger (SEG-54)

Pronto quando: a Rose arquiva um serviço de teste, abre o pacote no computador e confere que nada
dele ficou no portal.

---

## Fechamento · antes do primeiro cliente real

- [ ] Roteiro completo do teste de invasão ([`06-seguranca.md`](06-seguranca.md))
- [ ] Restauração de cópia de segurança testada
- [ ] Ensaio completo com cliente fictício, do cadastro ao arquivamento
- [ ] Cadastro real da Bela Bartók e "Enviar acesso"
