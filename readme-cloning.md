# 🚀 Panduan Setup, Hosting & Production Deployment — BMBK Stripmap

Dokumentasi lengkap untuk menjalankan project **Sistem Informasi Strip Map & Prediksi Kondisi Jalan BMBK Provinsi Lampung** di lingkungan **Local Development** maupun proses deployment ke **Production Server (Shared Hosting / cPanel & VPS Linux)**.

🔗 **Akses Production / Live URL:** [https://stripmap.bmbklampung.com/](https://stripmap.bmbklampung.com/)

---

## 📌 Sekilas Tentang Sistem
Aplikasi ini dibangun menggunakan arsitektur **Native PHP (MVC Pattern)** tanpa dependensi Composer yang rumit. Sistem mengelola data jalan provinsi dengan fitur utama:
- **Dashboard Eksekutif**: Ringkasan kemantapan jalan (Baik, Sedang, Rusak Ringan, Rusak Berat), jenis perkerasan, dan statistik per koridor.
- **Manajemen Ruas Jalan & GIS**: Master data ruas jalan provinsi, STA awal/akhir, koridor, kabupaten/kota, pemetaan koordinat, dan impor polyline rute KML/KMZ.
- **Visualisasi Strip Map Interaktif**: Diagram strip map per segmen 100m, komparasi kondisi vs perkerasan jalan, dan dokumentasi foto lapangan per STA.
- **Prediksi Kondisi Jalan (Ide Strip Map)**: Simulasi otomatis kondisi jalan pasca-penanganan kumulatif per tahun anggaran, perbandingan baseline vs target penanganan, serta perhitungan selisih/gap kemantapan.
- **Rekapitulasi & Export**: Rekap kemantapan dan jenis perkerasan per ruas jalan dengan filter koridor, serta modul export dan cetak laporan.

---

## ✅ Prerequisites (Kebutuhan Server)

| Kebutuhan | Versi Minimal | Keterangan |
|---|---|---|
| **PHP** | 8.0+ (disarankan 8.1 / 8.2) | Backend runtime |
| **MySQL / MariaDB** | 5.7+ / 10.4+ | Database server |
| **Web Server** | Apache (dengan `mod_rewrite`) / Nginx | Server web |
| **PHP Extensions** | `pdo_mysql`, `zip`, `fileinfo`, `mbstring`, `simplexml` | Harus aktif di `php.ini` |

---

# 💻 BAGIAN 1: Setup di Local Development (Laragon / XAMPP)

### 1. Clone atau Ekstrak Project
Tempatkan project di folder web server lokal:
- **Laragon**: `C:\laragon\www\bmbk-stripmap`
- **XAMPP**: `C:\xampp\htdocs\bmbk-stripmap`

### 2. Buat File `.env`
Salin atau buat file `.env` di root project (`bmbk-stripmap/.env`):
```env
APP_NAME='Stripmap - BMBK'
APP_URL=http://localhost/bmbk-stripmap/public/
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta

DB_HOST=localhost
DB_PORT=3306
DB_NAME=stripmap_db
DB_USER=root
DB_PASS=
```
> **Catatan:** File `config/database.php` sudah otomatis membaca data dari `.env`. Kamu tidak perlu mengedit file PHP lagi.

### 3. Eksekusi Schema Database
1. Buka phpMyAdmin (`http://localhost/phpmyadmin`) → klik tab **SQL**.
2. Copy-paste seluruh isi file [`database/schema.sql`](database/schema.sql) lalu klik **Go**.
3. Database `stripmap_db` beserta seluruh tabel, index, dan relasi langsung terbentuk otomatis.

### 4. Buka di Browser
Pastikan web server aktif, lalu buka:
```
http://localhost/bmbk-stripmap/public/
```

---

# 🌐 BAGIAN 2: Deployment ke Production Server

Aplikasi ini **100% siap untuk production** karena:
1. **Zero-Build**: Tidak memerlukan `npm run build` atau `composer install` di server.
2. **Environment-Based Config**: Cukup ubah kredensial di file `.env` tanpa mengubah kode aplikasi.
3. **Frontend CDN**: Asset CSS, JS, Icon, dan Font dimuat melalui CDN berkecepatan tinggi.

---

### Skenario A: Shared Hosting (cPanel)

Shared hosting adalah metode yang paling umum digunakan pada instansi pemerintah / dinas.

#### Langkah 1 — Upload File ke Server
Ada 2 metode struktur direktori yang direkomendasikan:

* **Opsi 1 (Paling Aman - Recommended):**
  1. Upload seluruh folder project `bmbk-stripmap` ke direktori root di luar `public_html`, misalnya di `/home/username/bmbk-stripmap/`.
  2. Pindahkan seluruh isi folder `public/` ke dalam folder `public_html/`.
  3. Edit file `public_html/index.php`, sesuaikan path bootstrap:
     ```php
     // Ubah dari:
     define('BASE_PATH', dirname(__DIR__));
     // Menjadi:
     define('BASE_PATH', '/home/username/bmbk-stripmap');
     ```

* **Opsi 2 (Menggunakan Subdomain / Subfolder):**
  1. Buat Subdomain di cPanel, contoh: `stripmap.bmbklampung.com`.
  2. Arahkan **Document Root** subdomain tersebut langsung ke subfolder `public/`, contoh: `public_html/bmbk-stripmap/public`.

#### Langkah 2 — Buat Database di cPanel
1. Buka cPanel → menu **MySQL Databases**.
2. Buat database baru (contoh: `bmbk_stripmap_db`).
3. Buat user database baru (contoh: `bmbk_user`) dengan password yang kuat.
4. Hubungkan user ke database tersebut dengan mencentang opsi **ALL PRIVILEGES**.

#### Langkah 3 — Import Schema Database
1. Buka menu **phpMyAdmin** di cPanel.
2. Pilih database yang baru saja dibuat di sidebar kiri.
3. Buka tab **SQL**, copy-paste seluruh isi [`database/schema.sql`](database/schema.sql).
   *(Catatan: Jika di cPanel tidak diizinkan menjalankan `CREATE DATABASE`, baris `CREATE DATABASE` dan `USE` di bagian atas file `schema.sql` bisa dilewati/dihapus, langsung jalankan mulai dari `CREATE TABLE IF NOT EXISTS`)*.

#### Langkah 4 — Konfigurasi `.env` Production
Buat file `.env` di server production:
```env
APP_NAME='Stripmap - BMBK'
APP_URL=https://stripmap.bmbklampung.com/
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta

DB_HOST=localhost
DB_PORT=3306
DB_NAME=bmbk_stripmap_db
DB_USER=bmbk_user
DB_PASS=PasswordKuatMySQL123!
```
> ⚠️ **PENTING UNTUK KEAMANAN:**
> - Pastikan `APP_DEBUG=false` agar detail query error tidak terlihat oleh publik jika terjadi gangguan.
> - Pastikan `APP_URL` menggunakan protokol `https://`.

#### Langkah 5 — Hak Akses Upload Foto (`public/uploads`)
Di cPanel File Manager, pastikan folder `uploads/` memiliki izin permission `755` atau `775` agar fitur upload foto lapangan per STA dapat menyimpan gambar.

---

### Skenario B: VPS / Cloud Server (Ubuntu / Debian)

Jika menggunakan VPS (DigitalOcean, AWS, IDCloudHost, dll):

#### 1. Setup Direktori & Permissions
```bash
# Clone project ke direktori web
cd /var/www
git clone https://github.com/username/bmbk-stripmap.git

# Berikan hak akses www-data ke folder upload
chown -R www-data:www-data /var/www/bmbk-stripmap/public/uploads
chmod -R 775 /var/www/bmbk-stripmap/public/uploads
```

#### 2. Konfigurasi Nginx (Virtual Host)
Buat file `/etc/nginx/sites-available/stripmap`:
```nginx
server {
    listen 80;
    server_name stripmap.bmbklampung.com;
    root /var/www/bmbk-stripmap/public;
    index index.php index.html;

    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?url=$uri&$args;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Blokir akses ke file tersembunyi seperti .env atau .git
    location ~ /\. {
        deny all;
    }
}
```
Aktifkan konfigurasi dan reload Nginx:
```bash
ln -s /etc/nginx/sites-available/stripmap /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx
```

#### 3. Pasang SSL Gratis (HTTPS) dengan Certbot
```bash
sudo certbot --nginx -d stripmap.bmbklampung.com
```

---

## ⚙️ Optimasi PHP untuk Produksi

Pastikan nilai berikut disesuaikan di `php.ini` server produksi agar upload foto STA dan import Excel KML tidak terputus:

```ini
upload_max_filesize = 20M
post_max_size = 25M
memory_limit = 256M
max_execution_time = 300
```

---

## 🌐 Frontend Dependencies (All via CDN)

Aplikasi tidak memerlukan build pipeline frontend (`npm/webpack/vite`). Seluruh library dimuat melalui CDN:

| Library | Versi | Fungsi |
|---|---|---|
| **Tailwind CSS** | 3.x CDN | Styling UI responsif |
| **Alpine.js** | 3.x CDN | Reaktivitas UI (toggle km/%, modal dialog, filter) |
| **Chart.js** | 4.x CDN | Grafik distribusi kemantapan & multi-tahun |
| **Chart.js DataLabels**| 2.2.0 | Label angka langsung pada grafik |
| **Leaflet.js** | 1.9.4 | Peta spasial GIS & visualisasi polyline rute |
| **SweetAlert2** | 11.x CDN | Dialog konfirmasi & notifikasi toast |

---

## ✅ Checklist Go-Live Production

- [ ] File `.env` sudah dibuat dengan kredensial database server production.
- [ ] `APP_DEBUG=false` sudah diterapkan di `.env`.
- [ ] `APP_URL` sudah mengarah ke domain/subdomain produksi (`https://...`).
- [ ] Database schema sudah diimport lengkap ke database production.
- [ ] Folder `public/uploads` memiliki izin tulis (permission `755`/`775`).
- [ ] Extension PHP (`pdo_mysql`, `zip`, `fileinfo`, `mbstring`, `simplexml`) aktif di server.
- [ ] SSL / HTTPS aktif dan halaman dashboard terbuka tanpa error koneksi.

---
*Dinas Bina Marga & Bina Konstruksi Provinsi Lampung*
