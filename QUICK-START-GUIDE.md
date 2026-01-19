# 🚀 QUICK START GUIDE - MPWA Installation

## ⚡ 3-Minute Installation

### Step 1: Start Docker (Already Done ✅)
```bash
docker-compose ps
```
**Expected:** All containers running and healthy

### Step 2: Open Installation Page
```
http://localhost:8000/install
```
**Expected:** Installation wizard appears

### Step 3: Complete Installation Wizard

#### Screen 1: Requirements Check
- All requirements should show ✅ green checkmarks
- Click "Next"

#### Screen 2: Database Configuration
**Use these values (already configured):**
```
Host: mysql
Database: mpwa
Username: mpwa_user
Password: mpwa_pass
```
- Click "Test Connection" (should succeed)
- Click "Next"

#### Screen 3: Admin Account
**Create your admin account:**
```
Username: admin (or your choice)
Email: your@email.com
Password: (choose a strong password)
```
- Click "Next"

#### Screen 4: Server Configuration
**Recommended settings:**
```
Server Type: localhost
Node.js Port: 3100
URL: http://localhost:3100
```
- Click "Next"

#### Screen 5: License (Optional)
- Leave blank if you don't have a license
- Click "Install"

### Step 4: Wait for Installation
- Installation takes ~1 minute
- Don't close the browser
- Wait for success message

### Step 5: Login
- You'll be redirected to dashboard
- Or go to: http://localhost:8000/login
- Use your admin credentials

---

## 🎯 What's Working

✅ **Web Application:** http://localhost:8000  
✅ **Installation Page:** http://localhost:8000/install  
✅ **Node.js API:** http://localhost:3100  
✅ **phpMyAdmin:** http://localhost:8082  
✅ **Database:** mysql:3306  

---

## 🔧 Useful Commands

### Check Status
```bash
docker-compose ps
```

### View Logs
```bash
docker-compose logs -f app
```

### Restart Services
```bash
docker-compose restart
```

### Stop Services
```bash
docker-compose down
```

### Start Services
```bash
docker-compose up -d
```

---

## 📞 Need Help?

### Check Logs
```bash
# Application logs
docker-compose logs app --tail=100

# Laravel logs
docker-compose exec app tail -f storage/logs/laravel.log
```

### Common Issues

**Issue:** Can't access http://localhost:8000  
**Solution:** Check if port 8000 is available
```bash
netstat -ano | findstr :8000
```

**Issue:** Database connection failed  
**Solution:** Wait for MySQL to be healthy
```bash
docker-compose ps mysql
```

**Issue:** 500 Error  
**Solution:** Clear cache
```bash
docker-compose exec app php artisan optimize:clear
```

---

## ✨ After Installation

### 1. Connect WhatsApp Device
- Go to "Devices" menu
- Click "Add Device"
- Scan QR code with WhatsApp

### 2. Test Features
- Send test message
- Create auto-reply
- Set up campaign

### 3. Configure Settings
- Update profile
- Set timezone
- Configure notifications

---

## 🎉 You're Ready!

**Installation is complete and verified.**  
**All systems operational.**  
**Start using MPWA now!**

---

**Quick Start Guide v1.0**  
**Last Updated:** January 19, 2026
