# Ponte — instruções para quem programa

Portal de acompanhamento de clientes da Dobra 9 (ponte.dobra9.com.br). Antes de qualquer tarefa,
leia `docs/00-visao-geral.md`, `docs/01-regras-de-negocio.md` e a fase atual em `docs/09-fases.md`.

## Como trabalhar com a Rose

- Tudo em **português do Brasil**: conversa, textos das telas, mensagens de commit, nomes de
  tabelas, colunas, rotas e classes de domínio (ver "Nomes" abaixo).
- A Rose não é programadora: explicar o que mudou **para quem usa**, sem jargão.
- **Uma fase por vez.** Não adiantar trabalho de fases seguintes. Cada fase termina com a Rose
  testando e aprovando.
- Regra nova ou mudada → atualizar o documento em `docs/` **antes** do código, e registrar em
  `docs/decisoes.md`.
- Salvar versão (commit) quando a Rose aprovar uma mudança. Nunca fazer push sem ela pedir.
- **Trabalhar só dentro desta pasta (`ponte/`).** A pasta `../site` (site publicado do dobra9.com.br)
  nunca é alterada: nada de editar, rodar build, publicar ou salvar versão lá. Se precisar de algo
  dela (logo, cor, estilo), copiar para `recursos/` aqui e usar a cópia.

## Stack

| Peça | Escolha |
|---|---|
| Linguagem | PHP 8.3+ |
| Framework | Laravel (versão estável mais recente no início da fase 1) |
| Login e 2FA | Laravel Fortify, com telas Blade próprias |
| Telas | Blade + componentes Blade + Tailwind CSS (via Vite) |
| Interatividade | Alpine.js **build CSP** (`@alpinejs/csp`), para manter a CSP rígida sem `unsafe-eval` |
| Banco | MySQL (Hostinger); SQLite em memória só nos testes |
| Filas | `database`, processadas pelo agendador (sem worker permanente: hospedagem compartilhada) |
| E-mail | Resend (mailer nativo do Laravel), remetente contato@dobra9.com.br |
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

## Comandos (depois da fase 1)

```bash
composer install && npm install
php artisan migrate --seed      # cria tabelas e os modelos de etapas de dados/modelos/
npm run dev                     # telas com recarga automática
php artisan test                # todos os testes (Pest)
./vendor/bin/pint               # arruma o estilo
./vendor/bin/phpstan analyse    # análise estática
composer audit                  # falhas conhecidas nas bibliotecas
```

## Antes de dizer "pronto"

- [ ] Testes passando (`php artisan test`), incluindo os de acesso entre clientes
- [ ] Pint e PHPStan sem erros
- [ ] Testado no navegador, no tamanho de celular
- [ ] Documento em `docs/` atualizado se alguma regra mudou
