# 06 · Segurança

A proteção vem de várias camadas, e nenhuma depende de o endereço ficar em segredo: todo subdomínio
com certificado aparece em listas públicas. O nome discreto e o caminho secreto só evitam chamar atenção.

Cada item tem um código (SEG-xx) para citar em testes e commits.

## Contas e login

| Código | O que | Como no Laravel |
|---|---|---|
| SEG-01 | Área da equipe em caminho secreto, 404 para não-equipe | Prefixo `config('ponte.caminho_equipe')` lido do `.env`; middleware `papel:equipe` faz `abort(404)` |
| SEG-02 | 2FA obrigatório para a equipe | Fortify `twoFactorAuthentication` com confirmação; middleware `2fa.obrigatorio` só libera E12 até configurar |
| SEG-03 | Senhas fortes | `Password::min(10)->uncompromised()` (consulta anônima a vazamentos); mesma regra no convite, troca e redefinição |
| SEG-04 | Senhas guardadas só como hash | `Hash` padrão (bcrypt custo 12 ou argon2id) |
| SEG-05 | Limite de tentativas | `RateLimiter` `login` 5/min por e-mail+IP; 20/min por IP; e-mail à equipe ao passar de 20 falhas/hora |
| SEG-06 | Mensagens que não revelam contas | Login, "esqueci a senha" e convite sempre com a mesma resposta, exista ou não o e-mail |
| SEG-07 | Sessão segura | `SESSION_SECURE_COOKIE=true`, `SESSION_ENCRYPT=true`, `http_only`, `same_site=lax`; `session()->regenerate()` no login; "sair de todos os aparelhos" com `Auth::logoutOtherDevices` |
| SEG-08 | Convite seguro | Token aleatório de 64 caracteres; só o **hash** no banco; 72 h; uso único; reenviar invalida os anteriores |
| SEG-09 | Equipe e cliente nunca se misturam | Login de cliente no caminho da equipe falha com erro genérico; equipe não entra pela tela do cliente |

## Acesso aos dados

| Código | O que | Como |
|---|---|---|
| SEG-10 | Cada cliente vê só o que é dele | Policies em todas as rotas + consultas a partir de `auth()->user()->cliente`; recurso alheio → 404 |
| SEG-11 | Teste de acesso cruzado obrigatório | Para cada rota do cliente, um teste Pest em que o cliente A tenta o recurso do cliente B e recebe 404 |
| SEG-12 | Proteção contra atribuição em massa | `$fillable` explícito; dados só de `$request->validated()` |
| SEG-13 | Cliente só baixa o próprio contrato | Rota `/conta/contrato` sem ID na URL: lê só o contrato do cliente logado; o caminho do arquivo nunca aparece na tela; teste específico |
| SEG-14 | Registro imutável | `Aprovacao` e `Atividade` lançam exceção em `updating`/`deleting`; usuário MySQL da aplicação sem permissão de `DROP` |

## Arquivos

| Código | O que | Como |
|---|---|---|
| SEG-20 | Fora da pasta pública | Disco `privado` em `storage/app/private`; nunca `storage:link` para ele |
| SEG-21 | Nome seguro | Salvo com UUID; o nome original só é exibido (escapado) |
| SEG-22 | Tipo verificado pelo conteúdo | Cliente: regra `mimetypes:` (finfo) + lista de RN-50; extensão derivada do tipo detectado. Equipe: qualquer tipo (RN-50), guardado sem extensão executável e sempre servido conforme SEG-24 |
| SEG-23 | Tamanho limitado | 50 MB por arquivo (`max:51200`) e `upload_max_filesize`/`post_max_size` no PHP |
| SEG-24 | Download seguro | Controller com policy; cabeçalhos `Content-Disposition: attachment` (exceto prévia de PNG/JPG/WEBP/PDF), `X-Content-Type-Options: nosniff` e `Content-Security-Policy: sandbox` em toda resposta de arquivo |
| SEG-25 | SVG e HTML nunca exibidos | Cliente não envia SVG; SVG, HTML e qualquer tipo fora de PDF/PNG/JPG/WEBP enviados pela equipe são sempre baixados, nunca exibidos inline |
| SEG-26 | Contrato criptografado | Conteúdo do PDF cifrado com `Crypt::encryptString` antes de gravar |
| SEG-27 | ZIPs nunca são abertos no servidor | Só guardados e devolvidos |

## Navegador e conexão

| Código | O que | Como |
|---|---|---|
| SEG-30 | HTTPS obrigatório | Redirecionamento no servidor + HSTS |
| SEG-31 | Cabeçalhos de segurança | Middleware global `CabecalhosSeguranca` com os mesmos valores do site (abaixo) |
| SEG-32 | CSP rígida | Sem script ou estilo inline; Alpine na build CSP; assets do Vite em arquivo |
| SEG-33 | CSRF | Padrão do Laravel em todo POST; nenhuma exceção |
| SEG-34 | XSS | Blade `{{ }}` sempre; `{!! !!}` proibido salvo HTML gerado pelo próprio sistema e revisado |
| SEG-35 | Links das entregas | Só `https://`; abertos com `rel="noopener noreferrer"` |

Cabeçalhos (SEG-31):
```
Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; font-src 'self'; connect-src 'self'; form-action 'self'; base-uri 'none'; object-src 'none'; frame-ancestors 'none'; upgrade-insecure-requests
Strict-Transport-Security: max-age=63072000; includeSubDomains
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=(), browsing-topics=()
Cross-Origin-Opener-Policy: same-origin
Cross-Origin-Resource-Policy: same-origin
X-Permitted-Cross-Domain-Policies: none
X-Robots-Tag: noindex, nofollow
```
Também: `robots.txt` com `Disallow: /`, sem sitemap, sem o caminho da equipe em lugar nenhum.

## Servidor e segredos

| Código | O que | Como |
|---|---|---|
| SEG-40 | Só `public/` exposto | Raiz do subdomínio aponta para `public/` (ver `08-infra-e-deploy.md`) |
| SEG-41 | Produção silenciosa | `APP_ENV=production`, `APP_DEBUG=false`, páginas de erro genéricas, `expose_php=Off` |
| SEG-42 | Segredos fora do repositório | `.env` só no servidor e nos Secrets do GitHub; `.env.example` sem valores |
| SEG-43 | Banco com usuário próprio | Usuário MySQL só deste banco, senha longa aleatória |
| SEG-44 | Logs sem dados pessoais | Não logar corpo de requisição, senhas, tokens nem conteúdo de arquivos |
| SEG-45 | Bibliotecas atualizadas | `composer audit` e `npm audit` na publicação (falha = não publica); alertas do GitHub ligados |
| SEG-46 | E-mails sem conteúdo sensível | O e-mail só avisa e leva ao portal; nunca anexa arquivos nem mostra itens de ajuste |

## Dados e LGPD

| Código | O que | Como |
|---|---|---|
| SEG-50 | Guardar só o necessário | Campos de RN-06; nada de CPF/CNPJ (vai pela pendência e é apagado no arquivamento) |
| SEG-51 | Campos pessoais criptografados | Cast `encrypted` em telefone, endereço, observações |
| SEG-52 | Página de privacidade | `/privacidade`: o que é guardado (inclusive o registro de entradas e ações com IP e navegador), para quê, por quanto tempo, como pedir remoção |
| SEG-53 | Remoção de verdade | Arquivamento (RN-62..RN-66) apaga arquivos e linhas; backups expiram em até 30 dias |
| SEG-54 | Cópias de segurança | Diárias, banco + arquivos, cifradas com senha, guardadas fora do servidor, retenção de 30 dias; restauração testada antes do 1º cliente e a cada 3 meses |
| SEG-55 | Registro de atividades | RN-41, com IP e navegador |

## Roteiro do teste de invasão (antes do 1º cliente)

Feito pela Dobra 9 contra o ambiente de produção já configurado, com dois clientes fictícios (A e B).

- [ ] Cliente A tenta abrir frente, etapa, arquivo, aprovar, pedir ajuste e baixar arquivo de B trocando IDs na URL → 404 em todos
- [ ] Cliente tenta abrir o caminho da equipe e rotas `/equipe`, `/admin`, `/painel` → 404
- [ ] Login: 6 tentativas erradas → bloqueio; mensagens iguais para e-mail existente e inexistente
- [ ] "Esqueci a senha" e convite: mesma resposta para e-mail existente e inexistente; token reutilizado → recusado; token vencido → recusado
- [ ] Sessão: cookie com `Secure`, `HttpOnly`, `SameSite`; ID muda após login; sair invalida a sessão
- [ ] CSRF: POST sem token → 419
- [ ] XSS: `<script>alert(1)</script>` e `"><img src=x onerror=alert(1)>` em itens de ajuste, nota, nome de arquivo, respostas do briefing, nome da empresa → aparece como texto
- [ ] Upload: `.php` renomeado para `.jpg`, SVG com script vindo do cliente, arquivo de 60 MB, nome `../../.env` → todos recusados
- [ ] Download de arquivo responde com `nosniff`, `sandbox` e `attachment`
- [ ] Atribuição em massa: enviar `papel=equipe`, `cliente_id=2`, `conta_como_revisao=false` nos formulários → ignorados
- [ ] Revisões: duas abas enviando ajuste ao mesmo tempo com 1 revisão restante → só uma passa
- [ ] Aprovação: tentar editar/apagar via qualquer rota → impossível
- [ ] Acesso direto: `/.env`, `/.git/`, `/storage/`, `/vendor/`, `/composer.json`, `*.zip`, `*.sql` → 404
- [ ] Erro forçado → página genérica, sem detalhes técnicos
- [ ] securityheaders.com nota A+; ssllabs.com nota A ou A+; Mozilla Observatory A+
- [ ] Backup: restaurar a cópia de ontem num banco limpo e conferir arquivos
