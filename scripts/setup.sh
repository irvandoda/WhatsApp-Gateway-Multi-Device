#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd -- "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "${ROOT_DIR}"

echo "==> MPWA bootstrap starting…"

if [ ! -f ".env" ]; then
  if [ ! -f ".env.example" ]; then
    echo "ERROR: .env.example tidak ditemukan. Buat manual lalu jalankan ulang."
    exit 1
  fi
  echo "==> Menyalin .env.example ke .env"
  cp .env.example .env
fi

if ! grep -q "^APP_KEY=" .env || grep -q "APP_KEY=$" .env; then
  echo "==> Menggenerate APP_KEY"
  php artisan key:generate --force --ansi
fi

if [ ! -d "vendor" ]; then
  echo "==> Menjalankan composer install"
  composer install --no-interaction --prefer-dist
else
  echo "==> Menjalankan composer install (update dependency baru jika ada)"
  composer install --no-interaction --prefer-dist
fi

if [ ! -d "node_modules" ] || [ ! -f "package-lock.json" ]; then
  echo "==> Menjalankan npm install untuk WhatsApp worker"
  npm install
else
  echo "==> Memastikan dependency Node terkini"
  npm install
fi

if [ ! -L "public/storage" ]; then
  echo "==> Membuat symbolic link storage"
  php artisan storage:link >/dev/null 2>&1 || true
else
  echo "==> Symbolic link storage sudah ada"
fi

echo "==> Menjalankan migrasi database"
php artisan migrate --force

SEED_BEHAVIOR="${SEED_DB:-auto}"
if [ "$SEED_BEHAVIOR" = "never" ]; then
  echo "==> Melewati proses seeding (SEED_DB=never)"
elif [ "$SEED_BEHAVIOR" = "always" ]; then
  echo "==> Menjalankan seeder (SEED_DB=always)"
  php artisan db:seed --force
else
  echo "==> Mode seeding otomatis (jalan hanya jika tabel users kosong)"
  USER_COUNT=$(php -r "require 'vendor/autoload.php'; \$app = require 'bootstrap/app.php'; \$kernel = \$app->make(Illuminate\\Contracts\\Console\\Kernel::class); \$kernel->bootstrap(); echo \\App\\Models\\User::count();")
  USER_COUNT=$(echo "${USER_COUNT:-0}" | tr -d '[:space:]')
  if [ "${USER_COUNT:-0}" -eq 0 ]; then
    echo "==> Users kosong, menjalankan seeder"
    php artisan db:seed --force
  else
    echo "==> Users sudah terisi (${USER_COUNT}), melewati seeding"
  fi
fi

echo "==> Membersihkan & meng-cache konfigurasi"
php artisan optimize:clear
php artisan config:cache
if php artisan route:cache; then
  echo "==> Route cache sukses"
else
  echo "==> Route cache gagal, membersihkan cache route agar tetap jalan"
  php artisan route:clear
fi

echo "==> Bootstrap selesai. Jalankan scripts/run-services.sh untuk memulai service."

