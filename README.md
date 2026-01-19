# 🚀 MPWA - WhatsApp Gateway Multi Device

[![License](https://img.shields.io/badge/license-CC%20BY--NC--ND%204.0-blue.svg)](https://creativecommons.org/licenses/by-nc-nd/4.0/)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777BB4.svg)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20.svg)](https://laravel.com/)
[![Node.js](https://img.shields.io/badge/Node.js-18.x-339933.svg)](https://nodejs.org/)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED.svg)](https://www.docker.com/)

**MPWA (Multi-Platform WhatsApp)** adalah platform open-source yang powerful untuk mengirim dan mengelola pesan WhatsApp secara otomatis. Mendukung multiple devices, campaign management, auto-reply, dan banyak fitur lainnya.

## ✨ Fitur Utama

### 📱 Multi-Device Support
- Kelola multiple WhatsApp devices dalam satu dashboard
- QR Code scanning untuk koneksi cepat
- Real-time device status monitoring
- Auto-reconnect mechanism

### 💬 Messaging Features
- **Blast Messages** - Kirim pesan massal ke banyak kontak
- **Campaign Management** - Atur campaign dengan scheduling
- **Auto Reply** - Balas otomatis berdasarkan keyword
- **Message Templates** - Template pesan yang dapat digunakan kembali
- **Message History** - Tracking semua pesan yang terkirim

### 🤖 AI Integration
- **ChatGPT Integration** - AI-powered chatbot
- **DALL-E Integration** - Generate gambar dari text
- **Gemini AI Support** - Google's AI model
- **Claude AI Support** - Anthropic's AI model
- **Custom System Instructions** - Personalisasi AI behavior

### 👥 Contact Management
- Import/Export contacts (CSV, Excel)
- Tag & categorize contacts
- Contact groups management
- Bulk operations

### 📊 Advanced Features
- **REST API** - Integrate dengan aplikasi lain
- **Webhook Support** - Real-time notifications
- **Subscription Plans** - Multi-tier user management
- **Two-Factor Authentication** - Enhanced security
- **Multi-Language** - Support 9+ languages
- **Ticket System** - Customer support management

### 🎨 User Interface
- Modern & responsive design (Vuexy theme)
- Dark mode support
- Mobile-friendly
- Intuitive dashboard

## 🛠️ Tech Stack

### Backend
- **Laravel 10.x** - PHP Framework
- **MySQL 8.0** - Database
- **PHP 8.2+** - Programming Language

### Frontend
- **Vuexy Theme** - Modern Admin Template
- **JavaScript/jQuery** - Interactive UI
- **Bootstrap 5** - CSS Framework

### WhatsApp Integration
- **Node.js 18.x** - WhatsApp Web API
- **Baileys** - WhatsApp Web Multi-Device Library
- **Socket.io** - Real-time communication

### Infrastructure
- **Docker & Docker Compose** - Containerization
- **Nginx** - Web Server
- **Supervisor** - Process Manager
- **Redis** - Caching (optional)

## 📋 Requirements

### Minimum Requirements
- **OS**: Linux, Windows, macOS
- **RAM**: 2GB minimum, 4GB recommended
- **Storage**: 10GB free space
- **Docker**: 20.10+ & Docker Compose 2.0+

### For Manual Installation
- PHP 8.2 or higher
- MySQL 8.0 or higher
- Node.js 18.x or higher
- Composer 2.x
- NPM or Yarn

### PHP Extensions Required
- curl
- fileinfo
- intl
- json
- mbstring
- openssl
- mysqli
- zip
- ctype
- dom

## 🚀 Quick Start (Docker)

### 1. Clone Repository
```bash
git clone https://github.com/irvandoda/WhatsApp-Gateway-Multi-Device.git
cd WhatsApp-Gateway-Multi-Device
```

### 2. Setup Environment
```bash
# Copy environment file
cp .env.example .env

# Edit .env file dengan konfigurasi Anda
nano .env
```

### 3. Start with Docker Compose
```bash
# Build and start containers
docker-compose up -d

# Wait for containers to be healthy (30-60 seconds)
docker-compose ps
```

### 4. Access Application
- **Web Interface**: http://localhost:8000
- **Node.js API**: http://localhost:3100

### 5. Installation Wizard
1. Buka browser ke http://localhost:8000
2. Ikuti wizard instalasi:
   - Check system requirements
   - Configure database
   - Create admin account
   - Configure server settings
3. Klik "Install" dan tunggu proses selesai
4. Login dengan credentials yang Anda buat

## 📦 Manual Installation

### 1. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Generate application key
php artisan key:generate
```

### 2. Database Setup
```bash
# Create database
mysql -u root -p
CREATE DATABASE mpwa;
EXIT;

# Run migrations
php artisan migrate --seed
```

### 3. Configure Environment
```bash
# Edit .env file
nano .env

# Set database credentials
DB_HOST=localhost
DB_DATABASE=mpwa
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Set application URL
APP_URL=http://localhost:8000

# Set Node.js server
WA_URL_SERVER=http://localhost:3100
PORT_NODE=3100
```

### 4. Start Services
```bash
# Terminal 1: Start Laravel
php artisan serve --host=0.0.0.0 --port=8000

# Terminal 2: Start Node.js server
node server.js

# Terminal 3: Start queue worker (optional)
php artisan queue:work
```

## 🔧 Configuration

### Environment Variables

#### Application Settings
```env
APP_NAME=MPWA
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost:8000
```

#### Database Configuration
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=mpwa
DB_USERNAME=mpwa_user
DB_PASSWORD=your_secure_password
```

#### WhatsApp Server
```env
WA_URL_SERVER=http://localhost:3100
PORT_NODE=3100
TYPE_SERVER=localhost
```

#### AI Integration (Optional)
```env
# OpenAI (ChatGPT & DALL-E)
CHATGPT_URL=https://api.openai.com/v1/chat/completions
CHATGPT_MODEL=gpt-4
DALLE_URL=https://api.openai.com/v1/images/generations
DALLE_SIZE=1024x1024

# Google Gemini
GEMINI_MODEL=gemini-pro

# Anthropic Claude
CLAUDE_URL=https://api.anthropic.com/v1/messages
CLAUDE_MODEL=claude-3-sonnet-20240229
```

#### Email Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

## 📖 Usage Guide

### Connecting WhatsApp Device

1. **Login to Dashboard**
   - Navigate to http://localhost:8000
   - Login with your admin credentials

2. **Add New Device**
   - Go to "Devices" menu
   - Click "Add Device"
   - Enter device name
   - Click "Save"

3. **Scan QR Code**
   - Click "Connect" on your device
   - Open WhatsApp on your phone
   - Go to Settings > Linked Devices
   - Scan the QR code displayed
   - Wait for connection confirmation

### Sending Messages

#### Single Message
```bash
POST /api/send-message
{
  "device_id": "device-123",
  "number": "628123456789",
  "message": "Hello from MPWA!"
}
```

#### Blast Message
1. Go to "Blast" menu
2. Click "Create New Blast"
3. Select device
4. Upload contact list (CSV/Excel)
5. Enter message content
6. Schedule or send immediately

#### Auto Reply
1. Go to "Auto Reply" menu
2. Click "Add Auto Reply"
3. Set keyword trigger
4. Set reply message
5. Configure options (delay, typing indicator, etc.)
6. Save and activate

### API Integration

#### Authentication
```bash
# Get API key from Settings > API
curl -X POST http://localhost:8000/api/send-message \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "device_id": "device-123",
    "number": "628123456789",
    "message": "Hello World"
  }'
```

#### Available Endpoints
- `POST /api/send-message` - Send single message
- `POST /api/send-media` - Send media (image, video, document)
- `POST /api/send-button` - Send button message
- `POST /api/send-list` - Send list message
- `GET /api/devices` - Get all devices
- `GET /api/device/{id}/status` - Get device status
- `POST /api/device/{id}/disconnect` - Disconnect device

## 🐳 Docker Commands

### Container Management
```bash
# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# Restart containers
docker-compose restart

# View logs
docker-compose logs -f

# View specific service logs
docker-compose logs -f app
```

### Application Commands
```bash
# Clear cache
docker exec mpwa-app php artisan cache:clear
docker exec mpwa-app php artisan config:clear

# Run migrations
docker exec mpwa-app php artisan migrate

# Create admin user
docker exec mpwa-app php artisan tinker
>>> $user = new App\Models\User();
>>> $user->username = 'admin';
>>> $user->email = 'admin@example.com';
>>> $user->password = Hash::make('password');
>>> $user->level = 'admin';
>>> $user->save();
```

### Database Backup & Restore
```bash
# Backup database
docker exec mpwa-mysql mysqldump -u mpwa_user -pmpwa_pass mpwa > backup_$(date +%Y%m%d_%H%M%S).sql

# Restore database
docker exec -i mpwa-mysql mysql -u mpwa_user -pmpwa_pass mpwa < backup_file.sql
```

## 🔒 Security

### Best Practices
1. **Change Default Credentials** - Ganti password admin setelah instalasi
2. **Use Strong Passwords** - Minimal 12 karakter dengan kombinasi huruf, angka, dan simbol
3. **Enable 2FA** - Aktifkan two-factor authentication
4. **Regular Updates** - Update aplikasi secara berkala
5. **Secure API Keys** - Jangan share API key di public
6. **HTTPS** - Gunakan SSL certificate untuk production
7. **Firewall** - Batasi akses ke port yang diperlukan saja
8. **Backup** - Lakukan backup database secara rutin

### File Permissions
```bash
# Set proper permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## 🐛 Troubleshooting

### Common Issues

#### 1. Cannot Connect to Database
```bash
# Check MySQL container
docker-compose ps mysql

# Check MySQL logs
docker-compose logs mysql

# Test connection
docker exec mpwa-app php artisan tinker
>>> DB::connection()->getPdo();
```

#### 2. Node.js Server Not Running
```bash
# Check Node.js process
docker exec mpwa-app ps aux | grep node

# Restart Node.js
docker exec mpwa-app supervisorctl restart nodejs

# Check Node.js logs
docker exec mpwa-app tail -f /var/log/supervisor/nodejs-stdout.log
```

#### 3. QR Code Not Showing
```bash
# Clear cache
docker exec mpwa-app php artisan cache:clear

# Restart container
docker-compose restart app

# Check browser console for errors
```

#### 4. Messages Not Sending
```bash
# Check device status
curl http://localhost:3100/device/status

# Check queue
docker exec mpwa-app php artisan queue:work

# Check logs
docker exec mpwa-app tail -f storage/logs/laravel.log
```

#### 5. Installation Error 500
- Pastikan CSRF exception sudah ditambahkan
- Clear cache: `php artisan cache:clear`
- Check logs: `tail -f storage/logs/laravel.log`
- Lihat `ANALISIS-MASALAH-INSTALASI.md` untuk detail

## 📚 Documentation

### Additional Resources
- [Installation Guide](INSTALLATION-SUCCESS.md) - Panduan instalasi lengkap
- [Docker Guide](README.Docker.md) - Panduan Docker deployment
- [Quick Start Guide](QUICK-START-GUIDE.md) - Panduan cepat memulai
- [Troubleshooting Guide](ANALISIS-MASALAH-INSTALASI.md) - Solusi masalah umum

### API Documentation
API documentation tersedia di `/api/documentation` setelah instalasi.

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Development Guidelines
- Follow PSR-12 coding standards
- Write meaningful commit messages
- Add tests for new features
- Update documentation

## 📝 License

This project is licensed under the **Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License**.

**Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.**

For details, visit: https://creativecommons.org/licenses/by-nc-nd/4.0/

### What this means:
- ✅ You can use this software for personal/non-commercial purposes
- ✅ You can share the software
- ❌ You cannot use it for commercial purposes without permission
- ❌ You cannot modify or create derivatives
- ❌ You must give appropriate credit

## 👨‍💻 Author

**Magd Almuntaser**
- Website: [OneXGen.com](https://www.onexgen.com)
- GitHub: [@irvandoda](https://github.com/irvandoda)

## 🙏 Acknowledgments

- [Laravel](https://laravel.com/) - The PHP Framework
- [Baileys](https://github.com/WhiskeySockets/Baileys) - WhatsApp Web Multi-Device API
- [Vuexy](https://pixinvent.com/demo/vuexy-html-bootstrap-admin-template/) - Admin Template
- All contributors and supporters

## 📞 Support

### Get Help
- 📧 Email: support@onexgen.com
- 💬 GitHub Issues: [Create an issue](https://github.com/irvandoda/WhatsApp-Gateway-Multi-Device/issues)
- 📖 Documentation: Check the docs folder

### Commercial Support
For commercial licensing, custom development, or enterprise support, please contact:
- Email: business@onexgen.com
- Website: https://www.onexgen.com

## 🗺️ Roadmap

### Upcoming Features
- [ ] WhatsApp Business API integration
- [ ] Advanced analytics dashboard
- [ ] Multi-user collaboration
- [ ] Message scheduling improvements
- [ ] Enhanced AI capabilities
- [ ] Mobile app (iOS & Android)
- [ ] Telegram integration
- [ ] Instagram integration

## ⭐ Star History

If you find this project useful, please consider giving it a star! ⭐

---

**Made with ❤️ by OneXGen Technology**

*Last Updated: January 2026*
