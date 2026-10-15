# Coding Principles
- Functions do ONE thing: A function should do one thing, do it well, and do only that. Prefer single-action classes (invokable controllers or Action classes) for business logic.
- Single Responsibility: Each class/module has one reason to change.
- Meaningful names: Names reveal intent. They must be informative, distinctive, pronounceable, and searchable.
- Comments minimally: Only for context and provenance when code intent isn't obvious. No emojis in comments.
- DRY (Don't Repeat Yourself): Avoid duplication, but don't abstract prematurely.
- Laravel conventions: Follow the standard directory structure.
- Dependency Injection: Inject via constructor or method parameter. No hardcoded new, no static facades in domain logic. This ensures testability.
- Avoid deep nesting: One level of abstraction per function. No if inside for inside if inside try.
- Wrap third-party code: Any external library (payment gateways, APIs, etc.) must be wrapped behind a thin interface owned by the project.
- Validate all input: Use Form Requests or in-service validation. Blade auto-escapes with {{ }}. Webhooks must verify signatures.
- Migrations are schema source of truth: Never alter production DB manually. Seeders are for development only.
- Prevent IDOR: Authorize every resource access (e.g., $this->authorize('view', $product)). No sequential IDs for sensitive resources? Use UUIDs if needed.
- Do not expose internals: Custom error pages (no debug in production). Log critical actions but never log passwords, full tokens, or payment details.
- Syntax check after changes: After modifying any PHP file, you MUST run php -l <file> on those specific files only. Never attempt to open a browser or run browser-based tests. If you modified multiple files, run the command for each one sequentially. If a syntax error is found, fix it immediately and re-check until the file passes lint.
- Aways soft delete for every data on database. Don't delete permanent, just soft delete.
- Every new function gets a test. Bug fixes get a regression test.
- Mock external I/O (API, DB, filesystem) with named fake classes, not inline stubs.
- Tests must be F.I.R.S.T: fast, independent, repeatable, self-validating, timely.
- Toda vez que você criar ou alterar uma migration, avisa que eu preciso rodar php artisan migrate, pra eu saber quando rodar ou não.


Portal de acompanhamento de clientes da Dobra 9 (ponte.dobra9.com.br). Antes de qualquer tarefa,
leia `docs/00-visao-geral.md`, `docs/01-regras-de-negocio.md` e a fase atual em `docs/09-fases.md`.

## Como trabalhar com a Rose

- Tudo em **português do Brasil**: conversa, textos das telas, mensagens de commit, nomes de
  tabelas, colunas, rotas e classes de domínio (ver "Nomes" abaixo).
- **Uma fase por vez.** Não adiantar trabalho de fases seguintes. Cada fase termina com a Rose
  testando e aprovando.
- Regra nova ou mudada → atualizar o documento em `docs/` **antes** do código, e registrar em
  `docs/decisoes.md`.
- Salvar versão (commit) quando a Rose aprovar uma mudança. Nunca fazer push sem ela pedir.
- **Trabalhar só dentro desta pasta (`ponte/`).** A pasta `../site` (site publicado do dobra9.com.br)
  nunca é alterada: nada de editar, rodar build, publicar ou salvar versão lá. Se precisar de algo
  dela (logo, cor, estilo), copiar para `recursos/` aqui e usar a cópia.
- **Não entrar no servidor (SSH na Hostinger) sem a Rose autorizar na hora**, nem só para olhar.
  A autorização vale para aquela tarefa, não para as seguintes. Publicação é passo a passo, pelo
  GitHub (`docs/08-infra-e-deploy.md`).

## Stack

| Peça | Escolha |
|---|---|
| Linguagem | PHP 8.3+ |
| Framework | Laravel (versão estável mais recente no início da fase 1) |
| Login e 2FA | Laravel Fortify, com telas Blade próprias |
| Telas | Blade + componentes Blade + Tailwind CSS (via Vite) |
| Interatividade | Alpine.js **build CSP** (`@alpinejs/csp`), para manter a CSP rígida sem `unsafe-eval` |
| Banco | MySQL (Hostinger); SQLite em memória nos testes do computador; MySQL nos testes da publicação |
| Filas | `database`, processadas pelo agendador (sem worker permanente: hospedagem compartilhada) |
| E-mail | SMTP da Hostinger (mailer `smtp` do Laravel) |
| Testes | Pest |
| Qualidade | Laravel Pint (estilo), Larastan nível 6+ (análise), `composer audit` (falhas conhecidas) |

Sem React/Vue/Livewire/Inertia. Páginas renderizadas no servidor, leves, que funcionam bem no celular.

## Nomes

- Domínio em português: `Cliente`, `Frente`, `Etapa`, `Entrega`, `Opcao`, `PedidoAjuste`,
  `Aprovacao`, `Arquivo`, `Atividade`, `Modelo`, `ModeloEtapa`.
- Tabelas no plural em português, snake_case: `clientes`, `frentes`, `etapas`, `pedidos_ajuste`.
- Estados e tipos como **PHP enums** com valores em snake_case português (`aguardando_cliente`).
- Código técnico do framework fica como o Laravel pede (`User`, `password`, `remember_token`).
- Sem acentos em nomes de código; com acentos em tudo que o usuário lê.

## Regras de código

- **Toda ação passa por uma Policy.** Nada de checar dono "na mão" no controller.
- Consultas de cliente **sempre** limitadas ao cliente logado (escopo global ou consulta a partir
  de `auth()->user()->cliente`). Nunca buscar por ID solto vindo da URL sem a policy.
- Mudanças de estado de etapa só por uma classe de ação (`app/Acoes/...`) que valida a transição
  (ver `docs/02-fluxos.md`) e grava a `Atividade`. Controllers não mudam `status` direto.
- Contagem de revisões dentro de transação com `lockForUpdate()` na frente (evita duas abas
  gastarem a mesma revisão).
- `Aprovacao` e `Atividade` são **somente inserção**: o model bloqueia update e delete.
- Validação sempre em Form Requests. Nada de `$request->all()` em `create`/`update`.
- `$fillable` explícito em todo model; `Model::shouldBeStrict()` ligado fora de produção.
- Datas de prazo em **dias úteis** pelo serviço `DiasUteis` (feriados em `config/feriados.php`).
- Arquivos só no disco `privado`; download só por controller com policy. Nunca `Storage::url`.
- Textos do usuário vêm de `lang/pt_BR/` (conteúdo em `docs/07-textos-e-emails.md`).

## Segurança (inegociável)

Lista completa em `docs/06-seguranca.md`. O mínimo que nenhum PR pode quebrar:

1. Área da equipe em prefixo secreto (`PONTE_CAMINHO_EQUIPE`), 2FA obrigatório, e quem não é
   equipe recebe **404** (não 403) nessas rotas.
2. Teste automático de "cliente A tenta abrir coisa do cliente B" para toda rota nova.
3. Limite de tentativas no login (5 por minuto por e-mail+IP).
4. Cabeçalhos de segurança e CSP rígida (ver doc).
5. Nenhum segredo no repositório. Nenhuma senha de cliente guardada, nunca.
6. Campos pessoais (telefone, endereço, observações) e o arquivo do contrato **criptografados**.



