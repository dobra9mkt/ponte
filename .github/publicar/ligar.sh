#!/usr/bin/env bash
# Roda no servidor, chamado por .github/workflows/publicar.yml.
# Uso: ligar.sh <versao> <caminho-do-php>
set -euo pipefail

VERSAO="$1"
PHP="$2"
APP="$HOME/domains/ponte.dobra9.com.br/ponte-app"
NOVA="$APP/releases/$VERSAO"
PUBLICO="$HOME/domains/ponte.dobra9.com.br/public_html"

if [ ! -f "$APP/shared/.env" ]; then
  echo "Falta o arquivo $APP/shared/.env (ver docs/08-infra-e-deploy.md)." >&2
  exit 1
fi

mkdir -p "$APP/shared/storage/app/private" \
         "$APP/shared/storage/framework/cache/data" \
         "$APP/shared/storage/framework/sessions" \
         "$APP/shared/storage/framework/views" \
         "$APP/shared/storage/logs"

# O servidor web roda como outro usuário: precisa atravessar estas pastas até public/, sem listá-las.
chmod 711 "$APP" "$APP/releases"
chmod 700 "$APP/shared"

ln -sfn "$APP/shared/.env" "$NOVA/.env"
ln -sfn "$APP/shared/storage" "$NOVA/storage"

if [ -L "$APP/current" ]; then
  "$PHP" "$APP/current/artisan" down --retry=30 || true
  trap '"$PHP" "$APP/current/artisan" up || true' ERR
fi

cd "$NOVA"
"$PHP" artisan migrate --force
"$PHP" artisan config:cache
"$PHP" artisan route:cache
"$PHP" artisan view:cache
"$PHP" artisan event:cache

ln -sfn "$NOVA" "$APP/current"

if [ ! -L "$PUBLICO" ]; then
  [ -e "$PUBLICO" ] && mv "$PUBLICO" "$PUBLICO.original-$(date +%Y%m%d%H%M%S)"
  ln -s "$APP/current/public" "$PUBLICO"
fi

"$PHP" "$APP/current/artisan" up

ls -1dt "$APP"/releases/*/ | tail -n +4 | xargs -r rm -rf
echo "Versão $VERSAO no ar."
