# WAGW – WhatsApp Gateway Multi-Device

**Laravel 12 + Node.js (Baileys)**  
Hybrid WhatsApp Automation Platform for high-volume and multi-user messaging

![Version](https://img.shields.io/badge/version-12.0.1-blue.svg)
![License](https://img.shields.io/badge/license-CC%20BY--NC--ND%204.0-lightgrey.svg)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-purple.svg)
![Node.js](https://img.shields.io/badge/Node.js-22%2B-green.svg)
![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)

---

## 📋 Table of Contents

- [Overview](#-overview)
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Requirements](#-requirements)
- [Quick Start](#-quick-start)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Running the Application](#-running-the-application)
- [REST API Documentation](#-rest-api-documentation)
- [Webhook Configuration](#-webhook-configuration)
- [Project Structure](#-project-structure)
- [Security Features](#-security-features)
- [Performance Optimization](#-performance-optimization)
- [Troubleshooting](#-troubleshooting)
- [Deployment](#-deployment)
- [Contributing](#-contributing)
- [License](#-license)
- [Support](#-support)

---

## 🚀 Overview

**WAGW (WhatsApp Gateway)** adalah platform hybrid enterprise-grade yang menggabungkan antarmuka web Laravel 12 dengan WhatsApp worker berbasis Node.js/Baileys. Platform ini dirancang khusus untuk tim marketing, customer service automation, dan integrator yang membutuhkan solusi WhatsApp messaging yang scalable dan powerful.

### 🎯 Use Cases

- **Marketing Automation**: Broadcast campaign, scheduled messaging, personalized content
- **Customer Service**: Auto-reply, chatbot AI, ticket management, multi-agent support
- **Business Integration**: REST API, webhook integration, CRM integration
- **Multi-tenant SaaS**: Subscription management, device quota, user management

### 🏗️ Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    WAGW Architecture                         │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌──────────────┐          HTTP / REST           ┌─────────┐│
│  │ Laravel 12   │  <──────────────────────────>  │ Node.js  ││
│  │              │     Socket.IO (Realtime)      │ Worker   ││
│  │ - Admin UI   │  <──────────────────────────> │          ││
│  │ - REST API   │                               │ - Baileys││
│  │ - Database   │                               │ - Message││
│  │ - Queue      │                               │ - Webhook││
│  └──────────────┘                               └─────────┘│
│         │                                              │     │
│         └────────────── MySQL ─────────────────────────┘     │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

---

## ✨ Features

### 🔧 Core Features

#### Device Management
- ✅ QR Code & Pairing Login (Multi-device support)
- ✅ Real-time Status Monitoring (Connected, Disconnected, Pairing)
- ✅ Device Quota Management per User
- ✅ Auto Availability Toggle
- ✅ Webhook Configuration per Device
- ✅ Device Grouping & Organization
- ✅ Bulk Device Operations

#### Autoreply Engine
- ✅ Keyword Matching (Exact & Partial)
- ✅ Regular Expression (Regex) Support
- ✅ Multi-format Response:
  - Text Messages
  - Media (Image, Video, Audio, Document)
  - Interactive Buttons
  - Interactive Lists
  - Stickers
  - Location Sharing
  - Contact Cards (vCard)
  - Product Catalog
  - Polls
- ✅ Delay & Typing Indicator Simulation
- ✅ Read Receipt Handling
- ✅ Context-aware Replies
- ✅ Multi-language Support

#### Campaign & Blast System
- ✅ Message Scheduling (Date & Time)
- ✅ Throttling Control (Rate Limiting)
- ✅ Contact Tagging & Segmentation
- ✅ Personalization Tokens (`{name}`, `{phone}`, `{custom}`)
- ✅ Pause/Resume Campaign
- ✅ Retry Mechanism with Fallback
- ✅ Campaign Analytics & Reports
- ✅ A/B Testing Support
- ✅ Template Management

#### AI Integration
- ✅ **OpenAI (ChatGPT)**: GPT-4, GPT-3.5, GPT-4 Turbo
- ✅ **Anthropic (Claude)**: Claude 3.5 Sonnet, Claude 3 Opus
- ✅ **Google (Gemini)**: Gemini Pro, Gemini Ultra
- ✅ Custom System Instructions
- ✅ Context-aware Conversations
- ✅ Multi-model Support per Device
- ✅ Conversation History Management
- ✅ Token Usage Tracking

#### Plugin System
- ✅ AI Bots (ChatGPT, Claude, Gemini)
- ✅ Sticker Bot
- ✅ Google Spreadsheet Integration
- ✅ Priority & Scope Configuration (All/Group/Personal)
- ✅ Custom Plugin Development
- ✅ Plugin Marketplace Support

#### Phonebook & Contact Management
- ✅ Excel Import/Export (CSV, XLSX)
- ✅ WhatsApp Group Sync
- ✅ Contact Segmentation & Tagging
- ✅ Bulk Contact Operations
- ✅ Contact History & Analytics
- ✅ Duplicate Detection

#### REST API & Webhooks
- ✅ Complete Messaging Endpoints
- ✅ Read Receipt Webhooks
- ✅ Typing Indicator Webhooks
- ✅ Call Rejected Webhooks
- ✅ API Key Authentication
- ✅ Rate Limiting
- ✅ Request/Response Logging
- ✅ API Documentation (Interactive)

#### User & Subscription Management
- ✅ Multi-tenant Support
- ✅ Device Quota Management
- ✅ Subscription States (Active/Inactive/Lifetime)
- ✅ API Key Generation & Management
- ✅ Role-based Access Control (RBAC)
- ✅ User Activity Logging
- ✅ Subscription Plans & Billing

### 💬 Message Types Supported

| Type             | Description                   | Features                            |
| ---------------- | ----------------------------- | ----------------------------------- |
| **Text**         | Plain text messages           | Emoji support, formatting           |
| **Media**        | Image, Video, Audio, Document | Caption, thumbnail, file size limit |
| **Sticker**      | WhatsApp stickers             | Animated & static                   |
| **Location**     | GPS coordinates               | Map preview, address               |
| **vCard**        | Contact cards                 | Name, phone, email, address         |
| **Product**      | Catalog items                 | Image, price, description, link     |
| **Button**       | Interactive buttons           | Up to 3 buttons, callback          |
| **List**         | Interactive lists             | Sections, rows, descriptions        |
| **Poll**         | Polls & surveys               | Multiple choice, vote counting      |
| **Text Channel** | Channel messages              | Broadcast to channels               |

### 🤖 AI Models Supported

- **OpenAI**: GPT-4, GPT-4 Turbo, GPT-3.5 Turbo, GPT-4o, GPT-4o-mini
- **Anthropic**: Claude 3.5 Sonnet, Claude 3 Opus, Claude 3 Haiku
- **Google**: Gemini Pro, Gemini Ultra, Gemini 1.5 Pro

---

## 🧱 Tech Stack

| Layer                | Technologies                      | Version      |
| -------------------- | --------------------------------- | ------------ |
| **Web Framework**    | Laravel                           | 12.x         |
| **PHP Runtime**      | PHP                               | 8.2+         |
| **WhatsApp Library** | @onexgen/baileys                  | 6.7.18+      |
| **Node.js Runtime**  | Node.js                           | 22+ (LTS)    |
| **Web Server**       | Express                           | 5.1.0+       |
| **Real-time**        | Socket.IO                         | 4.7.2+       |
| **Database**         | MySQL / MariaDB                  | 8.0+ / 10.6+ |
| **Cache/Queue**      | Redis (Optional)                  | Latest       |
| **Authentication**   | Laravel Sanctum                   | 4.0+         |
| **Image Processing** | Sharp, Intervention Image         | Latest       |
| **AI SDKs**          | OpenAI, Anthropic, Google GenAI   | Latest       |
| **Frontend**         | Vuexy Admin Template, Bootstrap 5 | Latest       |

---

## 📦 Requirements

### Minimum Requirements

- **PHP**: 8.2.0 atau lebih tinggi
- **Composer**: 2.7.0 atau lebih tinggi
- **Node.js**: 22.0.0 (LTS) atau 24.0.0+ (Current)
- **npm/yarn**: Versi terbaru
- **MySQL**: 8.0.0+ / **MariaDB**: 10.6.0+
- **Web Server**:
  - Nginx 1.18+ (recommended)
  - Apache 2.4+ dengan mod_rewrite
- **Memory**: Minimum 512MB RAM (disarankan 2GB+)
- **Storage**: Minimum 1GB free space

### PHP Extensions Required

```php
# Core Extensions
- BCMath
- Ctype
- cURL
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML

# Recommended Extensions
- GD atau Imagick (untuk image processing)
- MySQL PDO (untuk database)
- Redis (untuk cache & queue)
- Intl (untuk internationalization)
- Zip (untuk file compression)
- DOM (untuk XML/HTML processing)
```

### Node.js Requirements

- Node.js 22.x (LTS) atau 24.x (Current)
- npm 10.x+ atau yarn 1.22+
- Build tools (untuk native modules)

### Server Requirements

- **Operating System**: Linux (Ubuntu 20.04+, CentOS 8+), macOS, Windows Server
- **SSL Certificate**: Required untuk production (Let's Encrypt recommended)
- **Firewall**: Port 80 (HTTP), 443 (HTTPS), 3000 (Node.js worker - optional)

---

## ⚡ Quick Start

### Option 1: Docker (Recommended) 🐳

**Fastest way to get started with all services pre-configured!**

```bash
# 1. Clone repository
git clone https://github.com/irvandoda/WhatsApp-Gateway-Multi-Device.git wagw
cd wagw

# 2. Setup environment
cp .env.example .env
# Edit .env with your configuration

# 3. Start all services (Laravel, MySQL, Redis, Node.js, phpMyAdmin)
docker-compose up -d

# 4. Run migrations and seed
docker-compose exec app php artisan migrate --seed

# 5. Access application
# - Laravel App: http://localhost:8000
# - phpMyAdmin: http://localhost:8080
# - Node.js Worker: http://localhost:3000
```

### Option 2: Manual Installation

**Prerequisites Check:**

```bash
# Check PHP version
php -v  # Should be 8.2.0 or higher

# Check Composer
composer --version  # Should be 2.7.0 or higher

# Check Node.js
node -v  # Should be 22.0.0 or higher

# Check npm
npm -v  # Should be 10.0.0 or higher

# Check MySQL
mysql --version  # Should be 8.0.0 or higher
```

**5-Minute Setup:**

```bash
# 1. Clone repository
git clone https://github.com/irvandoda/WhatsApp-Gateway-Multi-Device.git wagw
cd wagw

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Configure database in .env file
# Edit .env and set your database credentials

# 5. Run migrations
php artisan migrate --seed

# 6. Start services (3 terminals)
# Terminal 1: Laravel
php artisan serve

# Terminal 2: Queue Worker
php artisan queue:work

# Terminal 3: Node.js Worker
node server.js

# 7. Access application
# Open browser: http://localhost:8000
# Default admin: admin@admin.com / password
```

---

## ⚙️ Installation

### Step-by-Step Installation

#### 1. Clone Repository

```bash
git clone https://github.com/irvandoda/WhatsApp-Gateway-Multi-Device.git wagw
cd wagw
```

#### 2. Install PHP Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

> **Note**: Untuk development, gunakan `composer install` tanpa flag `--no-dev`

#### 3. Install Node.js Dependencies

```bash
npm install
# atau
yarn install
```

#### 4. Environment Configuration

**Laravel Environment:**

```bash
cp .env.example .env
php artisan key:generate
```

**Node.js Worker Environment:**

```bash
cp server/.env.example server/.env
```

#### 5. Database Setup

Buat database MySQL:

```sql
CREATE DATABASE wagw_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'wagw_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON wagw_db.* TO 'wagw_user'@'localhost';
FLUSH PRIVILEGES;
```

#### 6. Configure Laravel `.env`

Edit file `.env`:

```env
APP_NAME="WAGW"
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wagw_db
DB_USERNAME=wagw_user
DB_PASSWORD=your_secure_password

# Cache & Queue
CACHE_DRIVER=file
QUEUE_CONNECTION=database
SESSION_DRIVER=file

# Redis (Optional but recommended)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Node.js Worker
WA_URL_SERVER=https://your-domain.com:3000
SOCKET_SERVER=https://your-domain.com:3000
PORT_NODE=3000

# API Configuration
API_KEY_SECRET=generate-random-secret-key-here

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"

# AI API Keys (Optional)
OPENAI_API_KEY=
CLAUDE_API_KEY=
GEMINI_API_KEY=
```

#### 7. Configure Node.js `server/.env`

Edit file `server/.env`:

```env
PORT=3000
NODE_ENV=production
APP_URL=https://your-domain.com

# Database (must match Laravel .env)
DATABASE_HOST=127.0.0.1
DATABASE_PORT=3306
DATABASE_USER=wagw_user
DATABASE_PASSWORD=your_secure_password
DATABASE_NAME=wagw_db

# AI API Keys
OPENAI_API_KEY=sk-your-openai-key
CLAUDE_API_KEY=sk-ant-your-claude-key
GEMINI_API_KEY=your-gemini-key

# Webhook
WEBHOOK_SECRET=your-webhook-secret-key

# SSL Configuration (if using HTTPS)
SSL_KEY_PATH=/path/to/ssl/key.pem
SSL_CERT_PATH=/path/to/ssl/cert.pem
```

#### 8. Run Database Migrations

```bash
php artisan migrate --seed
```

Ini akan membuat semua tabel database dan mengisi data awal (admin user, default plans, dll).

#### 9. Set File Permissions

**Linux/Mac:**

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

**Windows:**

Pastikan folder `storage` dan `bootstrap/cache` memiliki permission write.

#### 10. Optimize Application

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

#### 11. Build Frontend Assets (Optional)

```bash
npm run build
# atau
npm run production
```

---

## 🚀 Running the Application

### Development Mode

Jalankan 3 terminal terpisah:

**Terminal 1 - Laravel HTTP Server:**

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

**Terminal 2 - Laravel Queue Worker:**

```bash
php artisan queue:work --tries=3 --timeout=90
```

**Terminal 3 - Node.js WhatsApp Worker:**

```bash
node server.js
```

**Terminal 4 (Optional) - Development dengan Auto-reload:**

```bash
# Install nodemon globally
npm install -g nodemon

# Run with auto-reload
nodemon server.js
```

> **💡 Tip**: Untuk development yang lebih mudah, gunakan `php artisan queue:listen` yang akan auto-reload saat ada perubahan.

### Production Mode

#### Using PM2 (Node.js Worker)

```bash
# Install PM2 globally
npm install -g pm2

# Start Node.js worker
pm2 start server.js --name wagw-worker --instances 2

# Save PM2 configuration
pm2 save

# Setup PM2 to start on system boot
pm2 startup
pm2 save
```

**PM2 Commands:**

```bash
pm2 list              # List all processes
pm2 logs wagw-worker  # View logs
pm2 restart wagw-worker  # Restart worker
pm2 stop wagw-worker     # Stop worker
pm2 delete wagw-worker   # Remove from PM2
```

#### Using Supervisor (Laravel Queue)

Buat file `/etc/supervisor/conf.d/wagw-queue.conf`:

```ini
[program:wagw-queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/wagw/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/wagw/storage/logs/queue-worker.log
stopwaitsecs=3600
```

Reload supervisor:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start wagw-queue-worker:*
```

#### Using Systemd (Alternative)

Buat file `/etc/systemd/system/wagw-queue.service`:

```ini
[Unit]
Description=WAGW Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/wagw/artisan queue:work database --sleep=3 --tries=3

[Install]
WantedBy=multi-user.target
```

Enable dan start:

```bash
sudo systemctl enable wagw-queue
sudo systemctl start wagw-queue
sudo systemctl status wagw-queue
```

---

## 🌐 REST API Documentation

### Base URL

```
Production: https://your-domain.com/api
Development: http://localhost:8000/api
```

### Authentication

WAGW menggunakan API Key authentication. Sertakan API Key di header request:

**Option 1: Bearer Token**

```
Authorization: Bearer YOUR_API_KEY
```

**Option 2: X-Api-Key Header**

```
X-Api-Key: YOUR_API_KEY
```

### Generate API Key

1. Login ke admin panel
2. Navigate ke `Settings > API Keys`
3. Klik "Generate New API Key"
4. Copy dan simpan API Key (hanya ditampilkan sekali)

### API Endpoints

#### 📤 Send Messages

##### Send Text Message

```http
POST /api/send-message
Content-Type: application/json
Authorization: Bearer YOUR_API_KEY

{
    "device_id": "device_123",
    "to": "6281234567890",
    "message": "Hello from WAGW! 👋"
}
```

**Response:**

```json
{
    "status": true,
    "message": "Message sent successfully!",
    "data": {
        "message_id": "msg_abc123",
        "timestamp": "2026-01-17T10:30:00Z"
    }
}
```

##### Send Media

```http
POST /api/send-media
Content-Type: application/json

{
    "device_id": "device_123",
    "to": "6281234567890",
    "media_type": "image",
    "url": "https://example.com/image.jpg",
    "caption": "Check out this image!"
}
```

**Media Types:** `image`, `video`, `audio`, `document`

##### Send Button

```http
POST /api/send-button
Content-Type: application/json

{
    "device_id": "device_123",
    "to": "6281234567890",
    "message": "Choose an option:",
    "button": [
        {
            "id": "btn_yes",
            "text": "Yes ✅"
        },
        {
            "id": "btn_no",
            "text": "No ❌"
        }
    ]
}
```

##### Send List

```http
POST /api/send-list
Content-Type: application/json

{
    "device_id": "device_123",
    "to": "6281234567890",
    "title": "Main Menu",
    "name": "main_menu",
    "buttontext": "View Menu",
    "message": "Select an option:",
    "sections": [
        {
            "title": "Products",
            "rows": [
                {
                    "id": "product_1",
                    "title": "Product 1",
                    "description": "Description of product 1"
                },
                {
                    "id": "product_2",
                    "title": "Product 2",
                    "description": "Description of product 2"
                }
            ]
        }
    ]
}
```

##### Send Location

```http
POST /api/send-location
Content-Type: application/json

{
    "device_id": "device_123",
    "to": "6281234567890",
    "latitude": -6.2088,
    "longitude": 106.8456,
    "name": "Jakarta",
    "address": "Jakarta, Indonesia"
}
```

##### Send Contact (vCard)

```http
POST /api/send-vcard
Content-Type: application/json

{
    "device_id": "device_123",
    "to": "6281234567890",
    "name": "John Doe",
    "phone": "6281234567890",
    "email": "john@example.com",
    "organization": "WAGW"
}
```

##### Send Product

```http
POST /api/send-product
Content-Type: application/json

{
    "device_id": "device_123",
    "to": "6281234567890",
    "url": "https://example.com/product/123",
    "message": "Check out this product!"
}
```

##### Send Poll

```http
POST /api/send-poll
Content-Type: application/json

{
    "device_id": "device_123",
    "to": "6281234567890",
    "name": "Favorite Color",
    "option": ["Red", "Blue", "Green"],
    "countable": true
}
```

##### Send Sticker

```http
POST /api/send-sticker
Content-Type: application/json

{
    "device_id": "device_123",
    "to": "6281234567890",
    "url": "https://example.com/sticker.webp"
}
```

##### Send Text to Channel

```http
POST /api/send-text-channel
Content-Type: application/json

{
    "device_id": "device_123",
    "url": "https://whatsapp.com/channel/...",
    "message": "Broadcast message to channel"
}
```

#### 📥 Device Management

##### Get Device Information

```http
GET /api/device-info/{device_id}
Authorization: Bearer YOUR_API_KEY
```

##### Create Device

```http
POST /api/create-device
Content-Type: application/json

{
    "name": "Device 1",
    "user_id": 1
}
```

##### Generate QR Code

```http
POST /api/generate-qr/{device_id}
Authorization: Bearer YOUR_API_KEY
```

**Response:**

```json
{
    "status": true,
    "qr_code": "data:image/png;base64,iVBORw0KGgoAAAANS..."
}
```

##### Disconnect Device

```http
POST /api/disconnect-device/{device_id}
Authorization: Bearer YOUR_API_KEY
```

##### Delete Device

```http
DELETE /api/delete-device/{device_id}
Authorization: Bearer YOUR_API_KEY
```

#### 🔍 Utility Endpoints

##### Check WhatsApp Number

```http
GET /api/check-number?device_id={device_id}&number={number}
Authorization: Bearer YOUR_API_KEY
```

**Response:**

```json
{
    "status": true,
    "data": {
        "number": "6281234567890",
        "exists": true,
        "is_whatsapp": true,
        "is_business": false
    }
}
```

##### Get User Information

```http
GET /api/user-info
Authorization: Bearer YOUR_API_KEY
```

### Error Responses

```json
{
    "status": false,
    "message": "Error message here",
    "errors": {
        "field_name": ["Error detail"]
    }
}
```

**HTTP Status Codes:**

- `200` - Success
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

### Rate Limiting

API memiliki rate limiting default:

- **60 requests per minute** per API key
- **1000 requests per hour** per API key

Untuk meningkatkan limit, hubungi administrator.

---

## 🔔 Webhook Configuration

### Supported Events

| Event           | Description          | When Triggered              |
| --------------- | -------------------- | --------------------------- |
| incomingMessage | New message received | Pesan masuk dari WhatsApp   |
| readReceipt     | Message read         | Pesan dibaca oleh recipient |
| typing          | Typing indicator     | User sedang mengetik        |
| callRejected    | Call rejected        | Panggilan ditolak           |

### Webhook Format

```json
{
    "event": "incomingMessage",
    "device_id": "device_123",
    "timestamp": "2026-01-17T10:30:00Z",
    "data": {
        "from": "6281234567890",
        "to": "6289876543210",
        "message": "Hello",
        "message_id": "msg_abc123",
        "type": "text",
        "is_group": false,
        "group_id": null
    },
    "signature": "sha256_hash_here"
}
```

### Webhook Security

Setiap webhook request disertai signature untuk verifikasi:

```php
$signature = hash_hmac('sha256', json_encode($payload), $webhook_secret);
```

**Verification Example (PHP):**

```php
$receivedSignature = $_SERVER['HTTP_X_WAGW_SIGNATURE'];
$calculatedSignature = hash_hmac('sha256', file_get_contents('php://input'), $webhookSecret);

if (!hash_equals($calculatedSignature, $receivedSignature)) {
    http_response_code(401);
    exit('Invalid signature');
}
```

### Configure Webhook

1. Login ke admin panel
2. Navigate ke `Devices > Edit Device`
3. Scroll ke section "Webhook"
4. Masukkan webhook URL
5. Pilih events yang ingin diterima
6. Save configuration

---

## 📁 Project Structure

```
wagw/
├── app/                              # Laravel Application
│   ├── Console/
│   │   ├── Commands/                # Artisan Commands
│   │   └── Kernel.php               # Command Scheduler
│   ├── Exceptions/
│   │   └── Handler.php              # Exception Handler
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/                 # REST API Controllers
│   │   │   ├── Admin/               # Admin Panel Controllers
│   │   │   ├── Auth/                # Authentication Controllers
│   │   │   ├── Payments/            # Payment Gateway Controllers
│   │   │   └── User/                # User Controllers
│   │   ├── Middleware/              # Custom Middleware
│   │   ├── Requests/                # Form Request Validation
│   │   └── Kernel.php               # HTTP Kernel
│   ├── Models/                      # Eloquent Models
│   ├── Services/                    # Business Logic Services
│   │   └── Impl/                    # Service Implementations
│   ├── Providers/                   # Service Providers
│   ├── Repositories/                # Repository Pattern
│   └── Traits/                      # Reusable Traits
├── bootstrap/
│   ├── app.php                      # Application Bootstrap
│   └── cache/                       # Bootstrap Cache
├── config/                          # Configuration Files
│   ├── app.php                      # Application Config
│   ├── database.php                 # Database Config
│   ├── config.php                   # Custom Config
│   └── ...
├── database/
│   ├── factories/                   # Model Factories
│   ├── migrations/                  # Database Migrations
│   └── seeders/                     # Database Seeders
├── public/                          # Public Assets
│   ├── index.php                    # Entry Point
│   ├── themes/                      # Frontend Assets
│   └── ...
├── resources/
│   ├── lang/                        # Language Files
│   ├── themes/                      # Blade Templates
│   └── views/                       # View Components
├── routes/
│   ├── api.php                      # API Routes
│   ├── web.php                      # Web Routes
│   └── channels.php                 # Broadcast Channels
├── server/                          # Node.js WhatsApp Worker
│   ├── controllers/                 # Worker Controllers
│   │   ├── blast.js                 # Blast Controller
│   │   ├── incomingMessage.js      # Message Handler
│   │   └── messageProcessor.js     # Message Processor
│   ├── database/                    # Database Models
│   ├── lib/                         # Utilities & Helpers
│   │   ├── cache.js                 # Cache Helper
│   │   ├── helper.js                # General Helper
│   │   └── middleware.js            # Express Middleware
│   └── router/                      # Express Routes
├── storage/
│   ├── app/                         # Application Storage
│   ├── framework/                    # Framework Files
│   └── logs/                        # Log Files
├── tests/                           # PHPUnit Tests
├── .env.example                     # Environment Example
├── .gitignore                       # Git Ignore Rules
├── artisan                          # Laravel CLI
├── composer.json                    # PHP Dependencies
├── package.json                     # Node.js Dependencies
├── server.js                        # Node.js Entry Point
└── README.md                        # This File
```

---

## 🔒 Security Features

### Authentication & Authorization

- ✅ **API Key Authentication**: Secure API access dengan key-based auth
- ✅ **Two-Factor Authentication (2FA)**: TOTP-based 2FA untuk admin users
- ✅ **Session Management**: Secure session handling dengan encryption
- ✅ **Password Hashing**: bcrypt dengan cost factor optimal
- ✅ **Role-based Access Control (RBAC)**: Permission system untuk user management

### Data Protection

- ✅ **SQL Injection Prevention**: Eloquent ORM dengan parameter binding
- ✅ **XSS Protection**: Blade template escaping & Content Security Policy
- ✅ **CSRF Protection**: Laravel CSRF tokens untuk form submissions
- ✅ **Input Validation**: Comprehensive validation rules
- ✅ **Data Encryption**: Sensitive data encryption at rest

### Network Security

- ✅ **HTTPS Enforcement**: SSL/TLS encryption untuk semua connections
- ✅ **Rate Limiting**: API rate limiting untuk prevent abuse
- ✅ **Webhook Signature Verification**: HMAC-SHA256 signature validation
- ✅ **CORS Configuration**: Cross-origin resource sharing control
- ✅ **Firewall Rules**: IP whitelisting support

### Best Practices

- ✅ **Environment Variables**: Sensitive data di `.env` (not in code)
- ✅ **Secret Management**: API keys & secrets tidak di-commit ke Git
- ✅ **Logging**: Comprehensive logging untuk audit trail
- ✅ **Error Handling**: Secure error messages (no sensitive data exposure)

---

## ⚡ Performance Optimization

### Laravel Optimization

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize --classmap-authoritative

# Clear all caches
php artisan optimize:clear
```

### Database Optimization

```sql
-- Optimize tables
OPTIMIZE TABLE devices, messages, contacts;

-- Add indexes (if needed)
CREATE INDEX idx_device_user ON devices(user_id);
CREATE INDEX idx_message_device ON message_histories(device_id);
```

### Node.js Optimization

```bash
# Use PM2 cluster mode
pm2 start server.js --name wagw-worker -i max

# Enable Node.js optimizations
NODE_ENV=production node server.js
```

### Redis Configuration (Recommended)

```env
# .env
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

### CDN Configuration

Untuk production, gunakan CDN untuk static assets:

- Cloudflare
- AWS CloudFront
- Google Cloud CDN

---

## 🔧 Troubleshooting

### Common Issues

#### 1. PHP Version Error

**Problem**: `PHP 8.2.0 or higher required`

**Solution**:

```bash
# Check PHP version
php -v

# Update PHP (Ubuntu/Debian)
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip

# Update PHP (CentOS/RHEL)
sudo yum install php82 php82-cli php82-fpm php82-mysqlnd php82-mbstring php82-xml php82-curl
```

#### 2. Node.js Worker Not Connecting

**Problem**: Laravel tidak bisa connect ke Node.js worker

**Solution**:

- Check `WA_URL_SERVER` di `.env` Laravel
- Check `PORT` di `server/.env`
- Verify firewall rules (port 3000)
- Check Node.js worker logs: `pm2 logs wagw-worker`

#### 3. Database Connection Error

**Problem**: `SQLSTATE[HY000] [2002] Connection refused`

**Solution**:

```bash
# Check MySQL service
sudo systemctl status mysql

# Start MySQL
sudo systemctl start mysql

# Verify credentials in .env
# Test connection
php artisan tinker
>>> DB::connection()->getPdo();
```

#### 4. QR Code Not Generating

**Problem**: QR code tidak muncul saat pairing device

**Solution**:

- Check Node.js worker running
- Verify Socket.IO connection
- Check browser console untuk errors
- Clear browser cache

#### 5. Messages Not Sending

**Problem**: Pesan tidak terkirim via API

**Solution**:

- Verify device status (harus "Connected")
- Check API key valid
- Verify recipient number format (dengan country code)
- Check Node.js worker logs
- Verify database connection

#### 6. Queue Jobs Not Processing

**Problem**: Queue jobs stuck di database

**Solution**:

```bash
# Check queue worker running
ps aux | grep queue:work

# Restart queue worker
php artisan queue:restart

# Clear failed jobs
php artisan queue:flush

# Retry failed jobs
php artisan queue:retry all
```

### Debug Mode

Enable debug mode untuk development:

```env
# .env
APP_DEBUG=true
APP_ENV=local
```

**⚠️ Warning**: Jangan enable debug mode di production!

### Log Files

```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Queue logs
tail -f storage/logs/queue.log

# Node.js logs (PM2)
pm2 logs wagw-worker

# Node.js logs (direct)
tail -f server/logs/app.log
```

---

## 🚢 Deployment

### Production Checklist

- [ ] PHP 8.2+ installed & configured
- [ ] Node.js 22+ (LTS) installed
- [ ] MySQL 8.0+ / MariaDB 10.6+ configured
- [ ] Redis installed (optional but recommended)
- [ ] SSL certificate installed (Let's Encrypt)
- [ ] Environment variables configured (`.env`)
- [ ] Database migrations run (`php artisan migrate`)
- [ ] File permissions set correctly
- [ ] Queue worker configured (Supervisor/Systemd)
- [ ] Node.js worker running (PM2)
- [ ] Cron job configured (if using scheduler)
- [ ] Backup strategy in place
- [ ] Monitoring & logging configured
- [ ] Firewall rules configured
- [ ] Rate limiting configured
- [ ] CDN configured (optional)

### Nginx Configuration

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name your-domain.com;

    ssl_certificate /path/to/ssl/cert.pem;
    ssl_certificate_key /path/to/ssl/key.pem;

    root /var/www/wagw/public;
    index index.php;

    client_max_body_size 50M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Apache Configuration

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    Redirect permanent / https://your-domain.com/
</VirtualHost>

<VirtualHost *:443>
    ServerName your-domain.com
    DocumentRoot /var/www/wagw/public

    SSLEngine on
    SSLCertificateFile /path/to/ssl/cert.pem
    SSLCertificateKeyFile /path/to/ssl/key.pem

    <Directory /var/www/wagw/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Cron Job Setup

```bash
# Edit crontab
crontab -e

# Add Laravel scheduler
* * * * * cd /var/www/wagw && php artisan schedule:run >> /dev/null 2>&1
```

### Backup Strategy

```bash
# Database backup script
#!/bin/bash
mysqldump -u wagw_user -p wagw_db > /backup/wagw_$(date +%Y%m%d).sql

# Files backup
tar -czf /backup/wagw_files_$(date +%Y%m%d).tar.gz /var/www/wagw/storage
```

### Monitoring

Recommended monitoring tools:

- **Laravel Telescope** (development)
- **PM2 Monitoring** (Node.js)
- **New Relic** / **Datadog** (production)
- **Sentry** (error tracking)

---

## 🧪 Testing

### PHP Unit Tests

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter TestClassName

# Run with coverage
php artisan test --coverage
```

### Code Style

```bash
# Check code style
php artisan pint --test

# Fix code style
php artisan pint
```

### API Testing

Gunakan tools seperti:

- **Postman** - Collection available
- **Insomnia** - REST client
- **cURL** - Command line
- **Thunder Client** - VS Code extension

---

## 🌍 Multi-Language Support

WAGW mendukung 9 bahasa:

| Language                 | Code | Status     |
| ------------------------ | ---- | ---------- |
| English                  | en   | ✅ Complete |
| Bahasa Indonesia         | id   | ✅ Complete |
| العربية (Arabic)         | ar   | ✅ Complete |
| Español (Spanish)        | es   | ✅ Complete |
| Türkçe (Turkish)         | tr   | ✅ Complete |
| فارسی (Urdu)             | ur   | ✅ Complete |
| 中文 (Chinese)             | zh   | ✅ Complete |
| Azərbaycan (Azerbaijani) | az   | ✅ Complete |
| हिन्दी (Hindi)           | hi   | ✅ Complete |

### Adding New Language

1. Copy `resources/lang/en.json` ke `resources/lang/{code}.json`
2. Translate semua strings
3. Add language di admin panel: `Settings > Languages`
4. Set sebagai default (optional)

---

## 💳 Payment Gateways

WAGW mendukung 15+ payment gateway:

| Gateway      | Region         | Status   |
| ------------ | -------------- | -------- |
| Stripe       | Global         | ✅ Active |
| PayPal       | Global         | ✅ Active |
| Midtrans     | Indonesia      | ✅ Active |
| Xendit       | Southeast Asia | ✅ Active |
| Paystack     | Africa         | ✅ Active |
| Flutterwave  | Africa         | ✅ Active |
| Duitku       | Indonesia      | ✅ Active |
| Tripay       | Indonesia      | ✅ Active |
| Cashfree     | India          | ✅ Active |
| Paymob       | Middle East    | ✅ Active |
| Phonepe      | India          | ✅ Active |
| Mercado Pago | Latin America  | ✅ Active |
| Fawaterk     | Middle East    | ✅ Active |
| GenieBiz     | Global         | ✅ Active |
| Mcartaz      | Middle East    | ✅ Active |
| NowPayments  | Crypto         | ✅ Active |
| Paylink      | Middle East    | ✅ Active |

Configure payment gateway di admin panel: `Settings > Payment Gateways`

---

## 📊 Performance Benchmarks

### Message Throughput

- **Single Device**: ~30-50 messages/second
- **Multi-Device (10 devices)**: ~300-500 messages/second
- **Bulk Campaign**: ~1000+ messages/minute (with throttling)

### System Requirements

| Scale          | Users    | Devices | RAM   | CPU       | Storage |
| -------------- | -------- | ------- | ----- | --------- | ------- |
| **Small**      | 1-10     | 1-5     | 2GB   | 2 cores   | 20GB    |
| **Medium**     | 10-100   | 5-50    | 4GB   | 4 cores   | 50GB    |
| **Large**      | 100-1000 | 50-500  | 8GB+  | 8+ cores  | 100GB+  |
| **Enterprise** | 1000+    | 500+    | 16GB+ | 16+ cores | 500GB+  |

---

## 🔄 Upgrade Guide

### From Previous Version

```bash
# Backup database
mysqldump -u user -p wagw_db > backup.sql

# Backup files
tar -czf wagw_backup.tar.gz /path/to/wagw

# Pull latest code
git pull origin main

# Update dependencies
composer install --no-dev --optimize-autoloader
npm install

# Run migrations
php artisan migrate

# Clear caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
php artisan queue:restart
pm2 restart wagw-worker
```

---

## 🤝 Contributing

Kami sangat menghargai kontribusi dari komunitas! Berikut cara berkontribusi:

### How to Contribute

1. **Fork** repository
2. **Create** feature branch (`git checkout -b feature/AmazingFeature`)
3. **Commit** perubahan (`git commit -m 'Add some AmazingFeature'`)
4. **Push** ke branch (`git push origin feature/AmazingFeature`)
5. **Open** Pull Request

### Code Standards

- Follow PSR-12 coding standards
- Write unit tests untuk new features
- Update documentation jika diperlukan
- Keep commits atomic dan descriptive

### Reporting Issues

Gunakan GitHub Issues untuk melaporkan bug atau request fitur baru. Sertakan:

- Description yang jelas
- Steps to reproduce
- Expected vs actual behavior
- Environment details (PHP, Node.js versions)
- Screenshots (jika applicable)

---

## 📝 License

This project is licensed under the **Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International (CC BY-NC-ND 4.0)** License.

**What this means:**

- ✅ You can **share** and **use** the code
- ❌ You **cannot** use it for **commercial purposes**
- ❌ You **cannot** **modify** or **distribute** modified versions
- ✅ You must give **appropriate credit**

[View Full License](https://creativecommons.org/licenses/by-nc-nd/4.0/)

---

## 👨‍💻 Author & Credits

**Irvando Demas Arifiandani**

- 🌐 **Website**: irvandoda.my.id
- 📱 **WhatsApp**: +62 857 4747 6308
- 📧 **Email**: irvando.d.a@gmail.com
- 🐙 **GitHub**: @irvandoda

---

## 📞 Support & Contact

### Get Help

- 📧 **Email**: irvando.d.a@gmail.com
- 💬 **WhatsApp**: +62 857 4747 6308
- 🌐 **Website**: irvandoda.my.id
- 🐛 **Issues**: [GitHub Issues](https://github.com/irvandoda/WhatsApp-Gateway-Multi-Device/issues)

### Commercial Support

Untuk dukungan komersial, custom development, atau konsultasi:

- Hubungi via WhatsApp atau Email
- Visit website: irvandoda.my.id

---

## 🙏 Acknowledgments

Special thanks to:

- Baileys - WhatsApp Web API library
- Laravel - The PHP Framework for Web Artisans
- Vuexy - Admin Dashboard Template
- OneXGen - Baileys fork maintainer
- Semua contributor dan developer yang telah membantu pengembangan WAGW

---

## 📄 Changelog

### Version 12.0.1 (Current)

**New Features:**

- ✅ PHP 8.2 support
- ✅ Node.js 22+ (LTS) support
- ✅ Updated all dependencies to latest versions
- ✅ Improved performance & stability
- ✅ Enhanced security features

**Improvements:**

- 🔄 Better error handling
- 🔄 Optimized database queries
- 🔄 Improved API response times
- 🔄 Enhanced webhook system

**Bug Fixes:**

- 🐛 Fixed memory leaks in long-running processes
- 🐛 Fixed QR code generation issues
- 🐛 Fixed message delivery reliability

---

## 🎯 Roadmap

### Upcoming Features

- WhatsApp Business API integration
- Advanced analytics dashboard
- Multi-language autoreply
- Voice message support
- Video call integration
- Advanced reporting system
- Mobile app (iOS/Android)
- GraphQL API
- WebSocket API
- Advanced AI features

---

## ⭐ Star History

Jika project ini membantu Anda, pertimbangkan untuk memberikan ⭐ di GitHub!

---

**Made with ❤️ by Irvando Demas Arifiandani**

_Website: irvandoda.my.id | WhatsApp: +62 857 4747 6308_
