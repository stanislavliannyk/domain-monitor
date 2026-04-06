#!/usr/bin/env bash

set -e

cd "$(dirname "$0")/.."

echo "==> Сборка и запуск контейнеров..."
docker compose up -d --build

echo "==> Ожидание завершения инициализации (setup)..."
docker compose wait setup 2>/dev/null || true

echo "==> Установка npm-зависимостей и сборка фронтенда..."
npm install
npm run build

echo ""
echo "✓ Готово! Приложение доступно по адресу: http://localhost:${APP_PORT:-8080}"
echo "  Mailpit (почта):                       http://localhost:${MAILPIT_UI_PORT:-8025}"
