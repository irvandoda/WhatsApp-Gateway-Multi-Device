<h1 align="center">MPWA – WhatsApp Gateway Multi-Device</h1>

<p align="center">
  <strong>Laravel 11 + Node.js (Baileys)</strong><br/>
  Hybrid WhatsApp Automation Platform for high-volume and multi-user messaging
</p>

---

## 🚀 Overview

MPWA (irvandoda.my.id WhatsApp Gateway) menggabungkan antarmuka web Laravel dengan WhatsApp worker berbasis Node.js/Baileys. Platform ini ditujukan untuk tim marketing, CS automation, dan integrator yang membutuhkan:

- Multi-device dan multi-user management
- Kampanye broadcast berskala besar
- Autoreply cerdas + plugin AI (ChatGPT, Claude, Gemini)
- Integrasi REST API dan Webhook yang siap pakai

Struktur utamanya berada pada dua service:

```
┌──────────────┐          HTTP / REST           ┌───────────────┐
│ Laravel 11   │  <──────────────────────────> │ Node.js Worker │
│ - Admin UI   │     Socket.IO (Realtime)      │ - Baileys MD    │
│ - REST API   │  <──────────────────────────> │ - Message Flow  │
│ - Database   │                               │ - Webhook Pump  │
└──────────────┘                               └────────────────┘
```

---

## ✨ Fitur Unggulan

- **Device Center**: QR/pairing login, status monitor, limit per user, auto availability, webhook per device.
- **Autoreply Engine**: Keyword, regex, multi media (text, media, list, button, sticker, vcard, location) plus delay & typing indicator.
- **Campaign & Blast**: Scheduling, throttling, tagging, personalization token `{name}`, pause/resume, resend fallback.
- **Plugin System**: AI bots (ChatGPT, Claude, Gemini), Sticker bot, Google Spreadsheet IO; prioritas & scope (all/group/personal).
- **Phonebook & Tagging**: Import/Export Excel, sync grup WhatsApp, contact segmentation.
- **REST API & Webhook**: Complete messaging endpoints, read receipt/typing/call webhook hooks, API key middleware.
- **User & Subscription**: Multi-tenant, device quota, subscription state (active/inactive/lifetime), API key generator.

Detail teknis setiap modul dapat dilihat pada `ANALISIS_SISTEM.md`.

---

## 🧱 Tech Stack

| Layer                | Tools                                                                 |
|---------------------|-----------------------------------------------------------------------|
| Web Backend / API   | Laravel 11, Sanctum, MySQL, Excel Import (maatwebsite/excel)          |
| WhatsApp Worker     | Node.js 20+, @whiskeysockets/baileys, Express, Socket.IO, MySQL2       |
| Realtime & Webhooks | Socket.IO, custom webhook dispatcher                                  |
| Utilities           | OpenAI/Anthropic/Gemini SDK, Google APIs, Sharp, QRCode, Excel Export |

---

## 📦 Prasyarat

- PHP 8.2+
- Composer 2.6+
- Node.js 20+ & npm/yarn
- MySQL 8 / MariaDB 10.6+
- Redis (opsional, direkomendasikan untuk queue & rate limiting)

---

## ⚙️ Setup Cepat

1. **Clone & masuk folder**
   ```bash
   git clone https://github.com/<org>/whatsappgateway.git
   cd whatsappgateway
   ```

2. **Install dependency Laravel**
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

3. **Install dependency Node worker**
   ```bash
   yarn install    # atau npm install
   cp server/.env.example server/.env
   ```
   > Jika belum ada file contoh, duplikasi `.env.server`/`.env` sesuai kebutuhan Anda.

4. **Konfigurasi `.env` (Laravel)**
   ```dotenv
   APP_NAME="MPWA"
   APP_URL=https://gateway.local
   DB_HOST=127.0.0.1
   DB_DATABASE=mpwa
   DB_USERNAME=root
   DB_PASSWORD=secret
   BROADCAST_DRIVER=log
   QUEUE_CONNECTION=database
   SOCKET_SERVER=https://gateway.local:3000   # endpoint Node worker
   API_KEY_SECRET=your-api-key
   ```

5. **Konfigurasi `server/.env` (Node)**
   ```dotenv
   PORT=3000
   APP_URL=https://gateway.local
   DATABASE_HOST=127.0.0.1
   DATABASE_USER=root
   DATABASE_PASSWORD=secret
   DATABASE_NAME=mpwa
   OPENAI_API_KEY=
   CLAUDE_API_KEY=
   GEMINI_API_KEY=
   WEBHOOK_SECRET=
   ```

6. **Migrasi & seed database**
   ```bash
   php artisan migrate --seed
   ```

7. **Jalankan service**
   ```bash
   # Laravel
   php artisan serve --port=8000
   php artisan queue:work     # jalankan pada terminal terpisah

   # Node WhatsApp worker
   node server.js             # atau yarn dev untuk mode development
   ```

Gunakan process manager (Supervisor, PM2, systemd) untuk environment produksi.

---

## 🌐 REST API Singkat

Base URL mengikuti `APP_URL`. Sertakan header `Authorization: Bearer <API_KEY>` atau `X-Api-Key` sesuai middleware `CheckApiKey`.

Contoh: kirim teks sederhana

```bash
curl -X POST https://gateway.local/api/send-message \
  -H "Authorization: Bearer <API_KEY>" \
  -H "Content-Type: application/json" \
  -d '{
    "device_id": "DEV123",
    "to": "6281234567890",
    "message": "Halo, pesan otomatis dari MPWA!"
  }'
```

Endpoint lain tersedia di `routes/api.php`, mencakup `send-media`, `send-button`, `send-list`, `send-sticker`, `check-number`, `create-device`, dll.

---

## 🔔 Webhook & Plugin

- Atur URL webhook per-device dari panel admin (`Device > Webhook`).
- Event yang tersedia:
  - `incomingMessage`
  - `readReceipt`
  - `typing`
  - `callRejected`
- Plugin AI & Spreadsheet dapat diaktifkan per-device melalui `Plugin Manager`. Gunakan command start/stop serta konfigurasi `main_data` & `extra_data` untuk integrasi eksternal.

---

## 🧪 Testing & Quality

```bash
php artisan test          # Unit & feature tests
php artisan pint          # Code style (Laravel Pint)
```

Pastikan juga menjalankan `npm run test` pada modul yang Anda tambahkan di sisi Node jika diperlukan.

---

## 🚀 Deployment Notes

- Gunakan queue worker (Supervisor) untuk `php artisan queue:work`.
- Cache konfigurasi: `php artisan config:cache`, `route:cache`, `view:cache`.
- Jalankan Node worker dengan PM2 `pm2 start server.js --name mpwa-worker`.
- Pastikan folder `storage/` dan `bootstrap/cache/` writeable oleh web server.
- Aktifkan HTTPS dan pastikan webhook URL dapat dijangkau dari internet.

---

## 🤝 Kontribusi

1. Fork repo dan buat branch fitur.
2. Tambah/ubah fitur beserta dokumentasi.
3. Jalankan test & lint sebelum membuat pull request.

Kami menerima issue, enhancement, maupun integrasi plugin baru. Jelaskan use case Anda agar mudah ditinjau.

---

## 📄 Lisensi

Proyek ini berada di bawah lisensi MIT. Silakan gunakan secara bebas dengan tetap menjaga atribusi kepada irvandoda.my.id.

---

**Happy building automation!** Jika membutuhkan dukungan komersial, kunjungi https://irvandoda.my.id atau hubungi https://wa.me/6285747476308.

