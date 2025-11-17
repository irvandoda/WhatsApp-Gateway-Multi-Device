#!/usr/bin/env bash
set -euo pipefail

echo "[1/7] Checking nginx config syntax..."
if ! nginx -t; then
  echo "nginx config test failed. Aborting."
  exit 1
fi

echo "[2/7] Stopping nginx (if running)..."
if systemctl is-active --quiet nginx; then
  systemctl stop nginx || true
fi

echo "[3/7] Killing any leftover nginx processes..."
pkill -9 nginx || true

echo "[4/7] Removing stale PID file..."
rm -f /run/nginx.pid || true

echo "[5/7] Ensuring systemd override exists to auto-clean PID and align aaPanel PID path..."
mkdir -p /etc/systemd/system/nginx.service.d
mkdir -p /www/server/nginx/logs
cat >/etc/systemd/system/nginx.service.d/override.conf <<'OVR'
[Service]
ExecStartPre=/bin/rm -f /run/nginx.pid /www/server/nginx/logs/nginx.pid
ExecStartPost=/bin/ln -sf /run/nginx.pid /www/server/nginx/logs/nginx.pid
ExecStopPost=/bin/rm -f /run/nginx.pid /www/server/nginx/logs/nginx.pid
OVR

echo "[6/7] Reloading systemd and starting nginx..."
systemctl daemon-reload
systemctl start nginx

echo "[7/7] Verifying nginx status and ports..."
systemctl status nginx --no-pager || true
ss -ltnp | grep -E ':(80|443)\s' || true
ls -l /www/server/nginx/logs/nginx.pid || true

echo "Nginx repair completed."


