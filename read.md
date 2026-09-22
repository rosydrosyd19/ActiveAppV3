# Panduan Instalasi Aplikasi (Laravel + Filament) di Server Ubuntu

Panduan ini akan menjelaskan langkah-langkah untuk melakukan *deployment* atau instalasi aplikasi ini pada server Ubuntu (versi 20.04 / 22.04 / 24.04).

## 1. Persiapan Server (Kebutuhan Sistem)
Pastikan server Anda sudah diatur dan Anda telah masuk (login) sebagai user dengan hak akses `sudo`.
Aplikasi ini membutuhkan:
- **PHP** (Minimal versi 8.2)
- **Composer**
- **Database** (MySQL / MariaDB / PostgreSQL)
- **Web Server** (Nginx atau Apache, panduan ini menggunakan Nginx)
- **Node.js & NPM**

### Instalasi Nginx, PHP, dan Ekstensi
Jalankan perintah berikut di terminal server Anda:
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install nginx -y
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.2 dan ekstensi yang dibutuhkan Laravel
sudo apt install php8.2-fpm php8.2-cli php8.2-mysql php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip php8.2-intl -y
```

### Instalasi Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Instalasi Node.js & NPM
```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

### Instalasi Database (MySQL)
```bash
sudo apt install mysql-server -y
sudo mysql_secure_installation
```
Buat database untuk aplikasi:
```bash
sudo mysql
```
```sql
CREATE DATABASE active_app_v3;
CREATE USER 'app_user'@'localhost' IDENTIFIED BY 'password_rahasia';
GRANT ALL PRIVILEGES ON active_app_v3.* TO 'app_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 2. Persiapan Kode Aplikasi

Buat direktori untuk aplikasi Anda dan unggah (atau clone dari Git) file proyek ke dalam folder tersebut, misalnya di `/var/www/activeapp`.

```bash
sudo mkdir -p /var/www/activeapp
sudo chown -R $USER:$USER /var/www/activeapp
cd /var/www/activeapp

# (Jika menggunakan git clone, jalankan perintah di sini)
# git clone <url_repo_anda> .
```

### Instalasi Dependensi
Jalankan Composer dan NPM untuk menginstal semua *library* dan mem-build aset aplikasi.
```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

---

## 3. Konfigurasi Lingkungan (.env)

Salin file pengaturan `.env.example` menjadi `.env`.
```bash
cp .env.example .env
```

Buka dan edit file `.env`:
```bash
nano .env
```
Sesuaikan pengaturan utama berikut:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://domain-anda.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=active_app_v3
DB_USERNAME=app_user
DB_PASSWORD=password_rahasia
```

### Generate Kunci Aplikasi, Migrasi, & Storage Link
```bash
php artisan key:generate
php artisan migrate --force
php artisan storage:link
```

---

## 4. Pengaturan Hak Akses (Permissions)

Web server (Nginx) membutuhkan hak akses khusus ke folder `storage` dan `bootstrap/cache`.
```bash
sudo chown -R www-data:www-data /var/www/activeapp
sudo chmod -R 775 /var/www/activeapp/storage
sudo chmod -R 775 /var/www/activeapp/bootstrap/cache
```

---

## 5. Konfigurasi Nginx

Buat file *virtual host* (Server Block) baru di Nginx.
```bash
sudo nano /etc/nginx/sites-available/activeapp
```

Isikan konfigurasi berikut (sesuaikan `server_name` dengan domain atau IP server Anda):
```nginx
server {
    listen 80;
    server_name domain-anda.com;
    root /var/www/activeapp/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

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

Aktifkan konfigurasi Nginx dan muat ulang (restart) layanan Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/activeapp /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

---

## 6. Selesai!
Aplikasi kini sudah dapat diakses melalui browser dengan mengunjungi `http://domain-anda.com`. 
Untuk masuk ke halaman Filament Admin, tambahkan `/admin` di belakang URL (atau sesuai dengan *path* yang Anda konfigurasi di Filament).

> **Catatan Tambahan:** Jangan lupa untuk mengatur Let's Encrypt (Certbot) jika ingin menggunakan HTTPS (SSL).
