#!/bin/bash
# ============================================================
# CritiCare — mise à jour de l'application en une commande
# À lancer depuis le terminal web d'aaPanel (ou SSH) :
#   bash /www/wwwroot/192.168.100.27/deploy.sh
# (le script détecte tout seul le dossier où il se trouve)
# ============================================================

set -e  # arrêt immédiat si une étape échoue

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# PHP d'aaPanel (adapter "84" si autre version installée via l'App Store)
PHP_BIN="/www/server/php/84/bin/php"
[ -x "$PHP_BIN" ] || PHP_BIN="php"

cd "$APP_DIR"

echo "==> 1/6 Récupération du code (git pull) dans $APP_DIR"
# Dépôt privé : si le pull échoue en root, utiliser la ligne suivante à la place :
# sudo -u alili git -c safe.directory="$APP_DIR" -C "$APP_DIR" pull origin main
git -c safe.directory="$APP_DIR" pull origin main

echo "==> 2/6 Dépendances PHP (composer)"
if command -v composer >/dev/null 2>&1; then
    $PHP_BIN "$(command -v composer)" install --no-dev --optimize-autoloader --no-interaction
else
    $PHP_BIN /usr/local/bin/composer install --no-dev --optimize-autoloader --no-interaction
fi

echo "==> 3/6 Migrations de la base"
$PHP_BIN artisan migrate --force

echo "==> 4/6 Compilation des assets (Vite/Tailwind)"
npm install
npm run build

echo "==> 5/6 Caches Laravel"
$PHP_BIN artisan optimize:clear
$PHP_BIN artisan optimize

echo "==> 6/6 Permissions"
chown -R www:www storage bootstrap/cache

echo ""
echo "==> Mise à jour terminée. Pensez à Ctrl+F5 dans le navigateur."
