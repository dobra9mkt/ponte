# Ponte — portal de acompanhamento de clientes da Dobra 9

Portal em **ponte.dobra9.com.br** onde o cliente acompanha o projeto, envia o que falta e aprova
cada entrega, com **3 revisões por frente**. A equipe (Rose e Elias) administra por um caminho separado.

> **Status:** planejamento aprovado. Nenhuma linha de código ainda.
> O código só começa quando a Rose autorizar, pela fase 1 de [`docs/09-fases.md`](docs/09-fases.md).

Plano original (documento vivo, com o diagrama): https://claude.ai/code/artifact/4a9964e9-061d-47bc-aafa-cec1eaea092a

## O que tem nesta pasta

| Arquivo | Para quê |
|---|---|
| [`CLAUDE.md`](CLAUDE.md) | Regras para quem programa (pessoa ou IA): stack, convenções, segurança obrigatória |
| [`docs/00-visao-geral.md`](docs/00-visao-geral.md) | O que é o Ponte, vocabulário, decisões tomadas e o que fica fora |
| [`docs/01-regras-de-negocio.md`](docs/01-regras-de-negocio.md) | Todas as regras numeradas (RN-xx): revisões, prazos, convites, arquivamento |
| [`docs/02-fluxos.md`](docs/02-fluxos.md) | Estados de cada etapa e os fluxos passo a passo (diagramas) |
| [`docs/03-telas.md`](docs/03-telas.md) | Cada tela do cliente e da equipe, com esboço, campos e ações |
| [`docs/04-modelo-de-dados.md`](docs/04-modelo-de-dados.md) | Tabelas, colunas e relações do banco |
| [`docs/05-rotas-e-permissoes.md`](docs/05-rotas-e-permissoes.md) | Endereços do sistema e quem pode acessar cada um |
| [`docs/06-seguranca.md`](docs/06-seguranca.md) | Lista de segurança com a implementação em Laravel e o roteiro do teste de invasão |
| [`docs/07-textos-e-emails.md`](docs/07-textos-e-emails.md) | Todos os textos das telas e dos e-mails |
| [`docs/08-infra-e-deploy.md`](docs/08-infra-e-deploy.md) | Hostinger, subdomínio, publicação automática, tarefas agendadas, cópias de segurança |
| [`docs/09-fases.md`](docs/09-fases.md) | As 5 fases de construção, com tarefas e o que define "pronto" |
| [`docs/10-identidade-visual.md`](docs/10-identidade-visual.md) | Cores, fontes e componentes no padrão Dobra 9 |
| [`docs/11-testes.md`](docs/11-testes.md) | Testes obrigatórios por fase |
| [`docs/decisoes.md`](docs/decisoes.md) | Registro das decisões com data |
| [`dados/modelos/`](dados/modelos/) | Modelos de etapas (Rebranding, Site) prontos para virar dados iniciais |
| [`dados/briefing-rebranding.yaml`](dados/briefing-rebranding.yaml) | Perguntas do briefing de marca |
| [`dados/piloto-bela-bartok.yaml`](dados/piloto-bela-bartok.yaml) | Cronograma do cliente piloto, para montar o projeto dele no portal |
| [`recursos/marca/`](recursos/marca/) | Logos da Dobra 9 (SVG) e favicon |

## Ordem de leitura para quem vai programar

1. `CLAUDE.md`
2. `docs/00-visao-geral.md` e `docs/01-regras-de-negocio.md`
3. `docs/09-fases.md`, e dentro da fase atual, os documentos que ela aponta

## Quando o código começar

O Laravel é criado **dentro desta pasta** (fase 1). Os documentos continuam em `docs/` e `dados/`,
e cada mudança de regra é atualizada aqui antes de ir para o código.
