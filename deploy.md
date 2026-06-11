# Deploy Guide — lapor.mobilkom-trunking.my.id

Target: Ubuntu 22.04 LTS, Nginx, PHP 8.3, MySQL 8, SSL via Certbot.

---

## 0. Prasyarat DNS

Di panel domain kamu (Cloudflare/cPanel/etc), buat A record:

```
lapor.mobilkom-trunking.my.id  →  <IP_VM_KAMU>
```

Tunggu propagasi (biasanya <5 menit di Cloudflare, bisa sampai 1 jam di DNS lain).

---

## 1. Siapkan Server

SSH masuk ke VM:

```bash
ssh root@<IP_VM_KAMU>
# atau
ssh user@<IP_VM_KAMU>
```

Update sistem:

```bash
sudo apt update && sudo apt upgrade -y
```

---

## 2. Install PHP 8.3

```bash
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

sudo apt install -y php8.3 php8.3-fpm php8.3-mysql php8.3-mbstring \
  php8.3-xml php8.3-curl php8.3-zip php8.3-bcmath php8.3-gd \
  php8.3-intl php8.3-tokenizer php8.3-fileinfo
```

Cek versi:

```bash
php -v
# PHP 8.3.x
```

---

## 3. Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version
```

---

## 4. Install Node.js (untuk build Vite)

```bash
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install -y nodejs
node -v   # v22.x
npm -v
```

---

## 5. Install MySQL 8

```bash
sudo apt install -y mysql-server
sudo systemctl enable --now mysql
```

Amankan instalasi:

```bash
sudo mysql_secure_installation
# Jawab: Y untuk semua (set root password, hapus anonymous user, dll)
```

Buat database dan user:

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE mobilkom CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'mobilkom'@'localhost' IDENTIFIED BY 'GANTI_PASSWORD_KUAT';
GRANT ALL PRIVILEGES ON mobilkom.* TO 'mobilkom'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Catat: DB_USERNAME=`mobilkom`, DB_PASSWORD=`GANTI_PASSWORD_KUAT`.

---

## 6. Install Nginx

```bash
sudo apt install -y nginx
sudo systemctl enable --now nginx
```

---

## 7. Install Certbot (SSL)

```bash
sudo apt install -y certbot python3-certbot-nginx
```

---

## 8. Upload Kode ke Server

### Opsi A — Git (direkomendasikan)

Di server:

```bash
sudo mkdir -p /var/www/mobilkom
sudo chown $USER:$USER /var/www/mobilkom

cd /var/www/mobilkom
git clone <URL_REPO_KAMU> .
```

### Opsi B — rsync dari lokal

Di mesin lokal kamu:

```bash
rsync -avz --exclude='.git' --exclude='vendor' --exclude='node_modules' \
  /Users/linggaept/Documents/code/11-06-2026/mobilkom-1/ \
  user@<IP_VM>:/var/www/mobilkom/
```

---

## 9. Setup Aplikasi Laravel

```bash
cd /var/www/mobilkom

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node dependencies dan build aset
npm install
npm run build

# Salin dan isi .env
cp .env.example .env
nano .env
```

Isi `.env` minimal:

```env
APP_NAME="Mobilkom Laporan"
APP_ENV=production
APP_KEY=                          # akan di-generate
APP_DEBUG=false
APP_URL=https://lapor.mobilkom-trunking.my.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mobilkom
DB_USERNAME=mobilkom
DB_PASSWORD=GANTI_PASSWORD_KUAT

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

FILESYSTEM_DISK=local
```

Generate app key:

```bash
php artisan key:generate
```

Jalankan migrasi + seeder:

```bash
php artisan migrate --seed
```

---

## 10. Permission Storage

```bash
sudo chown -R www-data:www-data /var/www/mobilkom/storage
sudo chown -R www-data:www-data /var/www/mobilkom/bootstrap/cache
sudo chmod -R 775 /var/www/mobilkom/storage
sudo chmod -R 775 /var/www/mobilkom/bootstrap/cache
```

---

## 11. Konfigurasi Nginx

Buat file konfigurasi:

```bash
sudo nano /etc/nginx/sites-available/mobilkom
```

Isi:

```nginx
server {
    listen 80;
    server_name lapor.mobilkom-trunking.my.id;

    root /var/www/mobilkom/public;
    index index.php;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktifkan:

```bash
sudo ln -s /etc/nginx/sites-available/mobilkom /etc/nginx/sites-enabled/
sudo nginx -t          # harus: syntax is ok
sudo systemctl reload nginx
```

---

## 12. SSL dengan Certbot

```bash
sudo certbot --nginx -d lapor.mobilkom-trunking.my.id
# Ikuti prompt, pilih redirect HTTP → HTTPS
```

Certbot otomatis modifikasi config Nginx dan setup auto-renew. Cek:

```bash
sudo certbot renew --dry-run   # harus sukses
```

---

## 13. Setup Queue Worker (Supervisor)

Queue wajib jalan agar notifikasi terkirim.

```bash
sudo apt install -y supervisor
```

Buat config:

```bash
sudo nano /etc/supervisor/conf.d/mobilkom-worker.conf
```

Isi:

```ini
[program:mobilkom-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/mobilkom/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/mobilkom/storage/logs/worker.log
stopwaitsecs=3600
```

Aktifkan:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start mobilkom-worker:*
sudo supervisorctl status   # harus RUNNING
```

---

## 14. Optimasi Laravel Production

```bash
cd /var/www/mobilkom

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

## 15. Verifikasi

Buka browser: `https://lapor.mobilkom-trunking.my.id`

Harus tampil halaman login. Test login dengan:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@mobilkom.com | admin123 |
| Pimpinan | pimpinan@mobilkom.com | pimpinan123 |
| Teknisi | teknisi1@mobilkom.com | teknisi123 |
| Pelapor | pelapor@mobilkom.com | pelapor123 |

> **Ganti semua password default setelah deploy!**  
> Masuk sebagai admin → kelola user → edit password.

---

## Troubleshooting

### 500 Error

```bash
tail -f /var/www/mobilkom/storage/logs/laravel.log
sudo tail -f /var/log/nginx/error.log
```

### Permission denied

```bash
sudo chown -R www-data:www-data /var/www/mobilkom/storage /var/www/mobilkom/bootstrap/cache
```

### Queue tidak jalan

```bash
sudo supervisorctl status
sudo supervisorctl restart mobilkom-worker:*
```

### SSL gagal (domain belum propagasi)

Tunggu DNS propagasi dulu, lalu jalankan ulang certbot.

---

## Update Kode (Setelah Deploy Pertama)

```bash
cd /var/www/mobilkom

git pull origin main

composer install --no-dev --optimize-autoloader
npm run build

php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

sudo supervisorctl restart mobilkom-worker:*
```
