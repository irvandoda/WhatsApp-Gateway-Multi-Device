#!/usr/bin/env bash

set -euo pipefail

# ssl-apply.sh
# Sinkronisasi cert/key hasil generate ke path standar, update .env, perbaiki permission ACME,
# dan opsional restart NodeJS (pm2/systemd).
#
# Variabel opsional:
#   RESTART_NODE=[pm2|systemd|none]   default: none
#   PM2_APP_NAME=<nama_proses_pm2>    default: all
#   SYSTEMD_SERVICE=<nama_service>    contoh: whatsappgateway
#   FIX_ACME_PERMS=[true|false]       default: true
#
# Contoh:
#   RESTART_NODE=pm2 PM2_APP_NAME=server NODE_ENV=production bash scripts/ssl-apply.sh
#   RESTART_NODE=systemd SYSTEMD_SERVICE=whatsappgateway bash scripts/ssl-apply.sh

ROOT_DIR="$(cd -- "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "${ROOT_DIR}"

RESTART_NODE="${RESTART_NODE:-none}"
PM2_APP_NAME="${PM2_APP_NAME:-all}"
SYSTEMD_SERVICE="${SYSTEMD_SERVICE:-}"
FIX_ACME_PERMS="${FIX_ACME_PERMS:-true}"

info() { echo "[ssl-apply] $*"; }
warn() { echo "[ssl-apply][WARN] $*" >&2; }
err()  { echo "[ssl-apply][ERROR] $*" >&2; }

# Copy helper that skips when src and dst are the same file
safe_copy() {
  local src="$1"
  local dst="$2"
  local rs rc
  rs="$(readlink -f -- "$src" 2>/dev/null || echo "$src")"
  rc="$(readlink -f -- "$dst" 2>/dev/null || echo "$dst")"
  if [[ "$rs" == "$rc" ]]; then
    info "Melewati penyalinan (sama file): $src -> $dst"
    return 0
  fi
  cp -f -- "$src" "$dst"
}

# 1) Pastikan direktori ACME writable oleh php-fpm user (umumnya www-data)
if [[ "${FIX_ACME_PERMS}" == "true" ]]; then
  if [[ -d "${ROOT_DIR}/public/.well-known" ]]; then
    info "Memperbaiki permission ACME challenge directory"
    chown -R www-data:www-data "${ROOT_DIR}/public/.well-known" || warn "Gagal chown, lanjut..."
    find "${ROOT_DIR}/public/.well-known" -type d -exec chmod 755 {} + || true
    find "${ROOT_DIR}/public/.well-known" -type f -exec chmod 644 {} + || true
  else
    info "Membuat directory ACME challenge"
    mkdir -p "${ROOT_DIR}/public/.well-known/acme-challenge"
    chown -R www-data:www-data "${ROOT_DIR}/public/.well-known" || warn "Gagal chown, lanjut..."
    find "${ROOT_DIR}/public/.well-known" -type d -exec chmod 755 {} + || true
  fi
fi

# 2) Tentukan file cert/key yang tersedia dan sinkronkan ke nama umum
KEY_SRC=""
CERT_SRC=""

if [[ -f "${ROOT_DIR}/key.pem" && -f "${ROOT_DIR}/cert.pem" ]]; then
  KEY_SRC="${ROOT_DIR}/key.pem"
  CERT_SRC="${ROOT_DIR}/cert.pem"
  info "Menggunakan sumber lokal key.pem/cert.pem"
fi

# Jika ada nama gaya Let's Encrypt lokal, prefer gunakan itu
if [[ -f "${ROOT_DIR}/privkey.pem" && -f "${ROOT_DIR}/fullchain.pem" ]]; then
  KEY_SRC="${ROOT_DIR}/privkey.pem"
  CERT_SRC="${ROOT_DIR}/fullchain.pem"
  info "Menggunakan sumber lokal privkey.pem/fullchain.pem"
fi

if [[ -z "${KEY_SRC}" || -z "${CERT_SRC}" ]]; then
  warn "Tidak menemukan pasangan cert/key di root project. Melewati sinkronisasi."
else
  # Sinkronkan kedua nama agar keduanya ada
  info "Menyalin ${KEY_SRC} -> ${ROOT_DIR}/privkey.pem"
  safe_copy "${KEY_SRC}" "${ROOT_DIR}/privkey.pem"
  info "Menyalin ${CERT_SRC} -> ${ROOT_DIR}/fullchain.pem"
  safe_copy "${CERT_SRC}" "${ROOT_DIR}/fullchain.pem"
  # Juga simpan mirror sebagai key.pem/cert.pem untuk fallback
  info "Menyalin ${ROOT_DIR}/privkey.pem -> ${ROOT_DIR}/key.pem"
  safe_copy "${ROOT_DIR}/privkey.pem" "${ROOT_DIR}/key.pem"
  info "Menyalin ${ROOT_DIR}/fullchain.pem -> ${ROOT_DIR}/cert.pem"
  safe_copy "${ROOT_DIR}/fullchain.pem" "${ROOT_DIR}/cert.pem"
fi

# 3) Update .env agar server.js membaca path eksplisit
if [[ -f ".env" ]]; then
  info "Mengupdate .env (SSL_KEY_PATH, SSL_CERT_PATH, SSL_ENABLED=true)"
  # Tambah jika belum ada, lalu set nilainya
  grep -q "^SSL_KEY_PATH=" .env || echo "SSL_KEY_PATH=" >> .env
  grep -q "^SSL_CERT_PATH=" .env || echo "SSL_CERT_PATH=" >> .env
  grep -q "^SSL_ENABLED=" .env || echo "SSL_ENABLED=" >> .env

  sed -i "s#^SSL_KEY_PATH=.*#SSL_KEY_PATH=${ROOT_DIR}/privkey.pem#g" .env
  sed -i "s#^SSL_CERT_PATH=.*#SSL_CERT_PATH=${ROOT_DIR}/fullchain.pem#g" .env
  sed -i "s#^SSL_ENABLED=.*#SSL_ENABLED=true#g" .env

  # Cache config Laravel supaya variabel env terbaca konsisten
  php artisan config:clear >/dev/null 2>&1 || true
else
  warn ".env tidak ditemukan, melewati update env"
fi

# 4) Restart NodeJS sesuai opsi
case "${RESTART_NODE}" in
  pm2)
    info "Restart NodeJS via pm2 (${PM2_APP_NAME})"
    if command -v pm2 >/dev/null 2>&1; then
      pm2 reload "${PM2_APP_NAME}"
      pm2 status || true
    else
      err "pm2 tidak ditemukan di PATH"
      exit 1
    fi
    ;;
  systemd)
    if [[ -z "${SYSTEMD_SERVICE}" ]]; then
      err "SYSTEMD_SERVICE belum di-set. Contoh: SYSTEMD_SERVICE=whatsappgateway"
      exit 1
    fi
    info "Restart NodeJS via systemd (service: ${SYSTEMD_SERVICE})"
    systemctl restart "${SYSTEMD_SERVICE}"
    systemctl status "${SYSTEMD_SERVICE}" --no-pager || true
    ;;
  none|*)
    info "Melewati restart NodeJS (RESTART_NODE=${RESTART_NODE})"
    ;;
esac

info "Selesai. Pastikan akses HTTPS sudah aktif."


