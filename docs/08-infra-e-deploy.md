# 08 · Infraestrutura e publicação

## Ambientes

| Ambiente | Onde | Para quê |
|---|---|---|
| Local | Computador da Rose/Elias (Windows) com **Laravel Herd** (PHP + Composer) e Node 24 | Desenvolver e testar |
| Produção | Hostinger, `ponte.dobra9.com.br` | Clientes |

Sem ambiente intermediário na v1: os testes automáticos e o teste de invasão (SEG) cobrem isso.

## Hostinger: onde fica cada coisa

> ⚠ **O Ponte não pode ficar dentro de `domains/dobra9.com.br/public_html`.** A publicação do site
> usa `rsync --delete` nessa pasta e apagaria o portal; além disso, os
> arquivos do Laravel ficariam acessíveis por `dobra9.com.br/...`.

```
~/domains/ponte.dobra9.com.br/        (site próprio na conta u425831888)
├── ponte-app/
│   ├── releases/<data-commit>/      cada publicação numa pasta nova
│   ├── shared/
│   │   ├── .env                     segredos de produção (criado uma vez; senhas postas pela Rose)
│   │   └── storage/                 arquivos privados, logs, sessões (persistem entre publicações)
│   └── current -> releases/<última> atalho para a versão no ar
└── public_html -> ponte-app/current/public   (atalho: só a pasta public fica exposta)

A `ponte-app` fica ao lado da `public_html` (fora da parte pública) para aparecer no Gerenciador
de arquivos do hPanel, que só mostra a pasta do site.
```

Configuração única (fase 1):
1. hPanel → criar o subdomínio `ponte.dobra9.com.br` (pasta própria em `domains/`, **não** dentro do site).
2. hPanel → PHP do subdomínio na versão 8.3+; extensões `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `zip`, `gd`, `intl`, `sodium`; `expose_php=Off`; `upload_max_filesize=55M`, `post_max_size=60M`.
3. hPanel → SSL do subdomínio ativo + "Forçar HTTPS".
4. hPanel → banco MySQL `ponte` com usuário próprio e senha longa (SEG-43).
5. SSH → criar `~/ponte-app/shared/.env` (lista abaixo) e trocar `public_html` pelo atalho.
6. hPanel → tarefa agendada (cron) a cada minuto (abaixo).
7. Conferir a **retenção das cópias automáticas da Hostinger**: precisa ser ≤ 30 dias para cumprir RN-66.

A conta SSH da Hostinger é a mesma do site; os dados (servidor, porta, usuário) são pedidos à Rose
na preparação, com uma **chave SSH nova só para o Ponte**. A pasta do site não é usada nem alterada.

## Publicação automática (GitHub Actions)

Repositório privado próprio (`dobra9/ponte`). Publica a cada envio para `main`, que só acontece com
versão aprovada pela Rose. Arquivo `.github/workflows/publicar.yml` (criado na fase 1):

1. **Verificar:** `composer install`, `php artisan test` (num MySQL 8.4 do próprio GitHub, como na Hostinger), `vendor/bin/pint --test`, `vendor/bin/phpstan`, `composer audit`, `npm audit --omit=dev`. Qualquer falha para tudo.
2. **Montar:** `composer install --no-dev --optimize-autoloader`, `npm ci && npm run build`.
3. **Enviar:** `rsync` para `~/ponte-app/releases/<data-commit>/` (sem `.env`, sem `storage`, sem `node_modules`, sem `tests`).
4. **Ligar:** atalhos para `shared/.env` e `shared/storage`; `php artisan down`; `php artisan migrate --force`; `config:cache`, `route:cache`, `view:cache`, `event:cache`.
5. **Trocar:** `current` passa a apontar para a nova pasta; `php artisan up`. Mantém as 3 últimas versões (voltar = trocar o atalho).

Segredo do GitHub: `PONTE_SSH_KEY` (chave privada só do Ponte; a pública está em hPanel → Acesso SSH,
nome `ponte-github`). Servidor, porta, usuário (`u425831888`, conta de hospedagem do Ponte) e a
impressão digital do servidor ficam no próprio `publicar.yml`. Os comandos do passo 4 e 5 estão em
`.github/publicar/ligar.sh`, que também troca o `public_html` pelo atalho na primeira publicação.

## Tarefas agendadas

Cron do hPanel (conta `u425831888`), tipo Personalizado, a cada minuto (`* * * * *`). Caminho completo
e o PHP 8.4: o `php` comum do servidor é 8.1 e não roda o Ponte. Sem este cron nenhum e-mail sai,
porque todos passam pela fila:
```
/opt/alt/php84/usr/bin/php /home/u425831888/domains/ponte.dobra9.com.br/ponte-app/current/artisan schedule:run >> /home/u425831888/domains/ponte.dobra9.com.br/ponte-app/shared/storage/logs/cron.log 2>&1
```

Depois de mudar o `shared/.env`, rodar `php artisan config:cache` na pasta `current` (com o PHP 8.4):
a configuração fica guardada em cache e o `.env` novo só vale depois disso. Senhas no `.env` vão entre
aspas simples.

Agenda (em `routes/console.php`), fuso `America/Sao_Paulo`:

| Quando | Tarefa |
|---|---|
| A cada minuto | `queue:work --stop-when-empty --max-time=50` (e-mails e pacotes de arquivamento), sem sobreposição |
| De hora em hora, dias úteis 8h–18h | Lembretes de prazo (RN-31) |
| Segunda 08:00 | Resumo da semana para a equipe (RN-33) |
| Diariamente 07:00 | Avisos de "pronto para arquivar" (RN-61) |
| Diariamente 03:00 | Cópia de segurança (`backup:run`) + limpeza (`backup:clean`, retenção 30 dias) |
| Diariamente 04:00 | Apagar convites vencidos e pacotes de arquivamento não confirmados há 7+ dias |
| De hora em hora | Alerta de muitas falhas de login (SEG-05) |

## Cópias de segurança

Pacote `spatie/laravel-backup`: banco + `storage/app/private`, ZIP cifrado com senha
(`BACKUP_ARCHIVE_PASSWORD`), enviado para um armazenamento **fora da Hostinger**.
Sugestão de destino: Backblaze B2 ou outro serviço compatível com S3, com bucket privado.
Destino a confirmar com a Rose na fase 1. Retenção: 30 dias (RN-66).

## E-mail

SMTP da Hostinger (`smtp.hostinger.com`, porta 465, SSL), com uma caixa de e-mail criada no hPanel
para o envio. A senha dessa caixa fica só no `.env` do servidor.

As caixas de entrada do `dobra9.com.br` continuam na Weblink (registros MX). **Não mudar os MX**:
a Hostinger só envia. O SPF do domínio já inclui a Hostinger (`include:_spf.mail.hostinger.com`).

## Variáveis do `.env` de produção

```
APP_NAME="Ponte · Dobra 9"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ponte.dobra9.com.br
APP_KEY=                         # php artisan key:generate --show
APP_LOCALE=pt_BR
APP_TIMEZONE=America/Sao_Paulo

PONTE_CAMINHO_EQUIPE=            # 24+ caracteres aleatórios, letras e números
PONTE_EMAILS_EQUIPE=             # e-mails da Rose e do Elias, separados por vírgula

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=smtp
MAIL_SCHEME=smtps
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=                   # endereço da caixa criada na Hostinger
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=contato@dobra9.com.br
MAIL_FROM_NAME="Dobra 9"

FILESYSTEM_DISK=privado

BACKUP_ARCHIVE_PASSWORD=
BACKUP_S3_KEY=
BACKUP_S3_SECRET=
BACKUP_S3_REGION=
BACKUP_S3_BUCKET=
BACKUP_S3_ENDPOINT=
```

Os usuários da equipe são criados por comando (`php artisan ponte:criar-equipe`), nunca por tela,
e cada um configura o 2FA no primeiro acesso. Esquecer a senha com o 2FA configurado → "Esqueci minha
senha" no caminho da equipe (RN-05a). Sem 2FA configurado ou sem o aplicativo → o mesmo comando
redefine a senha.
