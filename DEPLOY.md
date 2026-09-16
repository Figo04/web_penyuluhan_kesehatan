# Deploy — Kenali Stunting

Panduan deploy ke shared hosting cPanel (Rumahweb). Stack: Laravel 12, PHP 8.3, MySQL.

Inti persoalannya satu: **shared hosting mengarahkan domain ke `public_html`, sedangkan
Laravel harus diarahkan ke folder `public/`.** Langkah 2 menyelesaikan itu; selebihnya
mengikuti.

---

## 1. Siapkan environment di cPanel

**Select PHP Version** → pilih **8.3**.

> `composer.json` memin platform ke PHP 8.3.16. Kalau hosting hanya menyediakan sampai
> 8.2, `composer install` akan menolak memasang. Naikkan versi PHP-nya, jangan turunkan
> pin-nya.

Aktifkan ekstensi berikut di halaman yang sama:

```
pdo_mysql   mbstring   openssl   tokenizer   xml
ctype       fileinfo   bcmath    gd          zip
```

`gd` dan `zip` wajib ada — PhpSpreadsheet membutuhkannya untuk export Excel. Tanpa
keduanya, menu Export akan error 500 padahal sisa aplikasi berjalan normal.

**MySQL Databases** → buat satu database dan satu user, beri user itu **All Privileges**
atas database tersebut. Catat ketiganya (nama database, username, password) untuk
langkah 4. Nama database dan user otomatis diberi prefiks username cPanel.

---

## 2. Struktur folder

Pilih salah satu sesuai jenis domain.

### Opsi A — domain bisa diatur document root-nya (disarankan)

Berlaku untuk addon domain dan subdomain. Tidak ada file yang perlu diedit.

Upload seluruh project ke luar `public_html`:

```
/home/USER/kenalistunting/      ← seluruh project apa adanya
```

Lalu **Domains → Manage → Document Root**, isi dengan:

```
/home/USER/kenalistunting/public
```

### Opsi B — document root terkunci di `public_html`

Umum terjadi pada domain utama. Project dipisah jadi dua bagian:

```
/home/USER/kenalistunting/      ← seluruh project KECUALI isi public/
/home/USER/public_html/         ← isi folder public/ (index.php, .htaccess, build/, favicon…)
```

Lalu edit dua baris di `public_html/index.php`:

```php
require __DIR__.'/../kenalistunting/vendor/autoload.php';
$app = require_once __DIR__.'/../kenalistunting/bootstrap/app.php';
```

### Yang tidak boleh terjadi

**Jangan menaruh seluruh project langsung di dalam `public_html`.** Kalau itu dilakukan,
`https://domainmu.com/.env` bisa dibuka siapa pun lewat browser — berisi password
database dan `APP_KEY`. Pada kedua opsi di atas, `.env` berada di luar document root.

---

## 3. Upload file

**Dengan akses SSH:**

```bash
cd /home/USER
git clone https://github.com/Figo04/web_penyuluhan_kesehatan.git kenalistunting
cd kenalistunting
composer install --no-dev --optimize-autoloader
```

**Tanpa akses SSH:**

1. Di komputer lokal, jalankan `composer install --no-dev --optimize-autoloader`
2. Zip seluruh project **termasuk folder `vendor/`**
3. Upload dan extract lewat **File Manager**

`vendor/` tidak ikut di repository, jadi harus dipasang di server atau ikut diupload.

**Tidak perlu menjalankan `npm` di server.** Seluruh halaman yang dipakai aplikasi ini
memuat CSS-nya inline di dalam Blade masing-masing; `@vite` hanya tersisa di dua view
bawaan Breeze (`layouts/guest.blade.php` dan `welcome.blade.php`) yang tidak terhubung ke
route mana pun. Folder `public/build/` sengaja tidak diikutkan ke repository dan memang
tidak dibutuhkan saat runtime.

---

## 4. Buat `.env`

Salin `.env.example` menjadi `.env`, lalu ubah bagian berikut:

```ini
APP_NAME="Kenali Stunting"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domainmu.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=kenw7319_kenali_stunting
DB_USERNAME=kenw7319_kstunting
DB_PASSWORD=XFU3MqpX!8FeZrg

SESSION_SECURE_COOKIE=true
LOG_LEVEL=warning

ADMIN_EMAIL=email-asli@domainmu.com
ADMIN_NAME="Admin Kenali Stunting"
ADMIN_PASSWORD=password-kuat-yang-baru
```

Empat baris yang paling menentukan:

| Baris                        | Kalau salah                                                                                           |
| ---------------------------- | ----------------------------------------------------------------------------------------------------- |
| `APP_DEBUG=false`            | Halaman error menampilkan stack trace **beserta seluruh isi environment**, termasuk password database |
| `APP_ENV=production`         | HTTPS tidak dipaksa, dan pengaman password admin default tidak aktif                                  |
| `SESSION_SECURE_COOKIE=true` | Cookie sesi bisa terkirim lewat koneksi http                                                          |
| `ADMIN_PASSWORD`             | Seeder **menolak jalan** kalau masih `admin123` di production — itu memang disengaja                  |

---

## 5. Jalankan sekali saat pemasangan

Lewat SSH, atau **Terminal** di cPanel:

```bash
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --class=AdminSeeder --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

chmod -R 775 storage bootstrap/cache
```

Kalau hosting tidak menyediakan SSH maupun Terminal, mintalah support Rumahweb
menjalankan blok perintah ini sekali dari direktori project.

`key:generate` hanya dijalankan **sekali saat pemasangan pertama**. Mengganti `APP_KEY`
setelah aplikasi dipakai akan membuat seluruh sesi yang berjalan menjadi tidak valid.

> ### ⚠️ Setelah `config:cache`, `.env` tidak lagi dibaca
>
> Laravel mengambil seluruh konfigurasi dari file cache, bukan dari `.env`. Setiap kali
> kamu mengubah `.env` — password database, `ADMIN_PASSWORD`, `APP_DEBUG`, apa pun —
> perubahan itu **tidak berpengaruh sampai config di-cache ulang**:
>
> ```bash
> php artisan config:cache
> ```
>
> Gejalanya membingungkan: password sudah diganti tapi yang berlaku masih yang lama,
> atau `APP_DEBUG=true` tidak memunculkan detail error. Kalau ada yang "tidak mau
> berubah", periksa ini lebih dulu sebelum mencari penyebab lain.

---

## 6. Verifikasi sebelum diserahkan

| #   | Cek                                      | Hasil yang benar                           |
| --- | ---------------------------------------- | ------------------------------------------ |
| 1   | Buka `https://domainmu.com/.env`         | **404 / Not Found**, bukan isi file        |
| 2   | Buka URL yang tidak ada, misal `/abc123` | Halaman error polos, **tanpa stack trace** |
| 3   | Buka `http://domainmu.com` (tanpa s)     | Teralihkan ke `https://`                   |
| 4   | Login admin → **Kelola Lokasi**          | Halaman terbuka, bisa menambah lokasi      |
| 5   | `/register`                              | Dropdown lokasi **terisi**                 |
| 6   | `/admin/login`, salah password 6 kali    | Muncul "Terlalu banyak percobaan login"    |
| 7   | Daftar responden baru                    | Dapat kode format `SEHAT0001`              |
| 8   | Beranda responden baru                   | Kartu Materi & Post-Test **terkunci** (🔒) |
| 9   | Admin → Export Excel dan CSV             | Kedua file terunduh dan bisa dibuka        |

**Nomor 5 paling sering terlewat.** Tabel lokasi kosong di pemasangan baru. Login admin
dan isi **Kelola Lokasi** lebih dulu — kalau tidak, semua responden terdaftar tanpa
lokasi, dan lokasi praktik bidan adalah salah satu variabel penelitian.

---

## 7. Update berikutnya

```bash
cd /home/USER/kenalistunting
php artisan down

git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan up
```

Jalankan `php artisan test` di lokal sebelum `git push` — ada 42 test yang menjaga alur
pre-test, materi, post-test, dan kontrol akses admin.

---

## 8. Kalau ada masalah

| Gejala                                      | Penyebab biasanya                                                               |
| ------------------------------------------- | ------------------------------------------------------------------------------- |
| Halaman putih kosong                        | `storage/` atau `bootstrap/cache/` tidak bisa ditulis → `chmod -R 775`          |
| "500 Server Error" tanpa keterangan         | Baca `storage/logs/laravel.log`, jangan menyalakan `APP_DEBUG` di server publik |
| Semua URL selain beranda jadi 404           | `mod_rewrite` mati, atau `public/.htaccess` tidak ikut terupload                |
| CSS/JS tidak muncul                         | Folder `public/build/` tidak ikut terupload                                     |
| Export Excel error 500                      | Ekstensi `gd` atau `zip` belum aktif (langkah 1)                                |
| Perubahan kode tidak terlihat               | Cache lama → `php artisan config:clear route:clear view:clear` lalu cache ulang |
| Admin dapat 403 di `/admin`                 | Migrasi `is_admin` belum jalan → `php artisan migrate --force`                  |
| Seeder berhenti dengan pesan ADMIN_PASSWORD | Memang disengaja. Set `ADMIN_PASSWORD` di `.env`, lalu ulangi                   |
