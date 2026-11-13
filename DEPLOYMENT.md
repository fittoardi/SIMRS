# Panduan Deployment Produksi — SIMRS RS Aisyah Medika

Dokumen ini memuat panduan teknis langkah demi langkah untuk melakukan proses deployment aplikasi **SIMRS (Sistem Informasi Manajemen Rumah Sakit) RS Aisyah Medika** ke server produksi (VPS / Dedicated Server).

---

## 1. Spesifikasi Server yang Direkomendasikan
- **Sistem Operasi**: Ubuntu 24.04 LTS (atau 22.04 LTS)
- **CPU**: 4 vCPU (disarankan 8 vCPU untuk beban puncak poliklinik)
- **RAM**: 8 GB (disarankan 16 GB dengan Redis cache & queue)
- **Storage**: SSD / NVMe minimal 100 GB (disarankan RAID 1)
- **Web Server**: Nginx 1.24+
- **PHP**: PHP 8.3 / 8.4 (ekstensi: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `gd`, `intl`, `json`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_mysql`, `redis`, `session`, `tokenizer`, `xml`, `zip`)
- **Database**: MySQL 8.0+ / MariaDB 10.11+ / PostgreSQL 16+
- **In-Memory Cache & Queue**: Redis 7+
- **Process Manager**: Supervisor
- **SSL**: Let's Encrypt Certbot

---

## 2. Alur Deployment dari Git Repository

### Langkah 1: Clone Repository & Set Izin Direktori
```bash
cd /var/www
git clone https://github.com/fittoardi/SIMRS.git simrs
cd /var/www/simrs

# Atur kepemilikan web server
sudo chown -R www-data:www-data /var/www/simrs
sudo chmod -R 775 /var/www/simrs/storage /var/www/simrs/bootstrap/cache
```

### Langkah 2: Install Dependensi PHP & Frontend
```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

### Langkah 3: Konfigurasi Environment File (`.env`)
Salin berkas `.env.example` ke `.env`:
```bash
cp .env.example .env
nano .env
```
Pastikan variabel produksi berikut dikonfigurasi secara tepat:
```ini
APP_NAME="RS Aisyah Medika"
APP_ENV=production
APP_KEY=base64:GENERATE_DENGAN_ARTISAN_KEY
APP_DEBUG=false
APP_URL=https://simrs.aisyahmedika.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simrs_production
DB_USERNAME=simrs_dbuser
DB_PASSWORD=KATA_SANDI_DATABASE_YANG_KUAT

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Konfigurasi Mailer (Resend / Brevo / SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=postmaster@aisyahmedika.id
MAIL_PASSWORD=KATA_SANDI_SMTP
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="layanan@aisyahmedika.id"
MAIL_FROM_NAME="RS Aisyah Medika"

# Konfigurasi Payment Gateway Midtrans (Produksi)
MIDTRANS_SERVER_KEY=SB-Mid-server-PRODUKSI
MIDTRANS_CLIENT_KEY=SB-Mid-client-PRODUKSI
MIDTRANS_IS_PRODUCTION=true

# Konfigurasi WhatsApp Gateway (Fonnte)
FONNTE_TOKEN=TOKEN_PRODUKSI_FONNTE
```

Generate application key & migrate database:
```bash
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --class=HospitalMasterDataSeeder --force
php artisan storage:link
```

---

## 3. Optimasi Cache Produksi Laravel
Jalankan perintah optimasi kecepatan runtime berikut:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan icons:cache
```

---

## 4. Konfigurasi Nginx VirtualHost

Buat konfigurasi file `/etc/nginx/sites-available/simrs.aisyahmedika.id`:
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name simrs.aisyahmedika.id;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name simrs.aisyahmedika.id;
    root /var/www/simrs/public;

    ssl_certificate /etc/letsencrypt/live/simrs.aisyahmedika.id/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/simrs.aisyahmedika.id/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    index index.php index.html;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    # Blokir akses ke file tersembunyi
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # PHP-FPM Socket
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
}
```
Aktifkan dan reload Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/simrs.aisyahmedika.id /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 5. Konfigurasi Background Queue Worker (Supervisor)

Buat file `/etc/supervisor/conf.d/simrs-worker.conf`:
```ini
[program:simrs-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/simrs/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/simrs/storage/logs/supervisor-worker.log
stopwaitsecs=3600
```
Jalankan supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start simrs-worker:*
```

---

## 6. Konfigurasi Scheduler Cron (Linux Crontab)
Jalankan `crontab -e -u www-data` dan tambahkan:
```cron
* * * * * cd /var/www/simrs && php artisan schedule:run >> /dev/null 2>&1
```

---

## 7. Checklist Go-Live Rumah Sakit
- [x] Pastikan `APP_DEBUG=false` di `.env`.
- [x] Pastikan SSL (HTTPS) aktif dengan skor SSL Labs Grade A.
- [x] Validasi bahwa `/admin` hanya dapat diakses staf berwenang.
- [x] Verifikasi koneksi webhook payment gateway (Midtrans/Xendit).
- [x] Jalankan backup database terjadwal ke penyimpanan cloud terpisah.
- [x] Lakukan uji coba pencetakan fisik ke printer thermal POS di loket admisi, kasir, dan farmasi.
- [x] Lakukan orientasi singkat aplikasi kepada perwakilan staf tiap unit kerja.
