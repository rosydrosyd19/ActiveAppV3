# Panduan Optimasi & Deployment ke Server Produksi (Production)

Dokumen ini berisi langkah-langkah penting untuk membuat aplikasi Laravel Filament ini berjalan dengan **sangat cepat dan ringan** ketika diunggah (*deploy*) ke server sungguhan (seperti VPS, cPanel, atau layanan *hosting* lainnya).

Di komputer lokal (tahap *development*), wajar jika aplikasi terasa lambat karena fitur *caching* (penyimpanan sementara) dimatikan agar perubahan kode Anda bisa langsung terlihat. Namun di server produksi, Anda **wajib** menyalakan *caching* ini.

---

## 🚀 1. Perintah Optimasi Pertama Kali (Saat Baru Rilis)

Setelah Anda memindahkan *source code* ke server dan mengatur database (`.env`), jalankan perintah berikut secara berurutan di terminal server Anda untuk mengunci dan mempercepat aplikasi:

```bash
# 1. Mengompilasi Composer Autoloader
composer install --optimize-autoloader --no-dev

# 2. Mengompilasi dan mengunci semua konfigurasi, rute, dan event
php artisan optimize

# 3. Mengompilasi file antarmuka (Blade/View) agar siap disajikan
php artisan view:cache

# 4. Mengompilasi komponen khusus Filament
php artisan filament:optimize

# 5. Mengompilasi aset (CSS/JS) milik Filament dan Livewire
php artisan filament:upgrade
```

---

## 🔄 2. Apa yang Harus Dilakukan Setelah Anda Memperbarui Kode (Update / Git Pull)?

Setiap kali Anda mengubah kode (menambah fitur baru, mengubah tampilan, atau menambah rute) lalu mengunggahnya ke server produksi, sistem **tidak akan membaca perubahan Anda** karena masih menggunakan cache yang lama.

Oleh karena itu, setiap kali **setelah selesai update**, Anda wajib menjalankan siklus perintah ini:

```bash
# 1. Bersihkan seluruh cache lama
php artisan optimize:clear
php artisan filament:clear-cached-components

# 2. Lakukan Migrasi Database (Jika Anda menambah tabel/kolom baru di lokal)
php artisan migrate --force

# 3. Kunci dan optimalkan kembali (Sama seperti langkah awal)
php artisan optimize
php artisan view:cache
php artisan filament:optimize
```

---

## 💡 3. Tips Tambahan untuk Server Produksi

1. **Pastikan `APP_ENV` dan `APP_DEBUG`**:
   Di file `.env` server Anda, pastikan Anda telah mengatur:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```
   *(Penting: Jika `APP_DEBUG=true` di server produksi, error sistem bisa terlihat oleh orang luar dan membahayakan keamanan data).*

2. **Aktifkan OPcache (PHP)**:
   Pastikan ekstensi PHP `opcache` aktif di server Anda. Ekstensi ini menyimpan kode PHP yang sudah di-*compile* di memori (RAM), sehingga server tidak perlu membaca kode dari _hard disk_ setiap kali ada pengunjung. Ini bisa meningkatkan kecepatan hingga 3-4x lipat.

3. **Storage Link**:
   Jangan lupa menjalankan `php artisan storage:link` agar file gambar atau dokumen yang diunggah bisa diakses oleh publik.
