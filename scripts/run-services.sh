#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd -- "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "${ROOT_DIR}"

APP_HOST="${APP_HOST:-0.0.0.0}"
APP_PORT="${APP_PORT:-8000}"
QUEUE_WORKER="${QUEUE_WORKER:-default}"
NODE_ENVIRONMENT="${NODE_ENV:-production}"

php artisan config:cache >/dev/null 2>&1 || true

echo "==> Menjalankan Laravel HTTP server di ${APP_HOST}:${APP_PORT}"
php artisan serve --host="${APP_HOST}" --port="${APP_PORT}" &
PHP_SERVER_PID=$!

echo "==> Menjalankan queue worker (${QUEUE_WORKER})"
php artisan queue:work --queue="${QUEUE_WORKER}" --tries=1 --backoff=3 &
QUEUE_PID=$!

echo "==> Menjalankan WhatsApp worker (server.js) dalam mode ${NODE_ENVIRONMENT}"
NODE_ENV="${NODE_ENVIRONMENT}" node server.js &
NODE_PID=$!

function cleanup() {
  echo "==> Menghentikan service…"
  kill "$NODE_PID" "$QUEUE_PID" "$PHP_SERVER_PID" >/dev/null 2>&1 || true
  wait "$NODE_PID" "$QUEUE_PID" "$PHP_SERVER_PID" 2>/dev/null || true
}

trap cleanup SIGINT SIGTERM

wait -n "$PHP_SERVER_PID" "$QUEUE_PID" "$NODE_PID"
cleanup

