# Hobby Store: Sistem Manajemen Toko Hobi

Aplikasi web back-office untuk satu toko hobi (model kit, action figure, diecast, dan perlengkapan hobi). Aplikasi mencatat stok produk, penjualan, pembelian stok (restock), dan menampilkan ringkasan keuangan untuk pemilik toko.

Dibuat sebagai tugas UAS **Web Application Development** (Universitas Cakrawala) dengan Laravel 13, MySQL, dan Bootstrap 5.

- **Aplikasi online:** ISI_URL_APLIKASI
- **Repository:** ISI_URL_REPOSITORY

## Pengguna dan hak akses

| Fitur | Owner | Staff |
|---|---|---|
| Dashboard keuangan | ✅ | ❌ |
| Kategori (tambah/ubah/hapus) | ✅ | ❌ |
| Supplier (tambah/ubah/hapus) | ✅ | ❌ |
| Produk: lihat daftar | ✅ | ✅ |
| Produk: tambah/ubah/hapus (termasuk foto) | ✅ | ❌ |
| Restock (pengeluaran) | ✅ | ❌ |
| Penjualan: buat dan lihat | ✅ | ✅ |

Pendaftaran (register) bersifat publik dan akun baru otomatis berperan **Staff**. Akun Owner dibuat lewat seeder.

## Akun demo (hanya untuk pengujian)

| Role | Email | Password |
|---|---|---|
| Owner | owner@hobbystore.test | password |
| Staff | staff@hobbystore.test | password |

## Fitur utama

- Autentikasi manual: register, login, logout, session, dan pembatasan percobaan login
- Role-based access control lewat middleware `EnsureRole`
- CRUD kategori, supplier, dan produk (dengan upload foto)
- Penjualan multi-item: stok berkurang otomatis memakai `DB::transaction` dan row locking
- Restock multi-item: stok bertambah otomatis, mencatat pengeluaran
- Dashboard: pemasukan, pengeluaran, selisih kas (bulan berjalan dan semua waktu), stok menipis, produk terlaris
- Validasi input di sisi server, proteksi CSRF, escaping output (XSS), dan query terparameter (SQL Injection)

## Teknologi

Laravel 13, PHP 8.3, MySQL, Blade, Bootstrap 5 (CDN), dan JavaScript.

## Struktur database (8 tabel buatan sendiri)

`users`, `categories`, `suppliers`, `products`, `sales`, `sale_items`, `restocks`, `restock_items`.

Relasi utama: `categories` 1-N `products`; `sales` N-M `products` lewat `sale_items`; `restocks` N-M `products` lewat `restock_items`; `suppliers` 1-N `restocks`; `users` 1-N `sales` dan `restocks`.

## Struktur MVC

```
app/
  Http/
    Controllers/   AuthController, DashboardController, CategoryController,
                   SupplierController, ProductController, SaleController, RestockController
    Middleware/    EnsureRole
  Models/          User, Category, Supplier, Product, Sale, SaleItem, Restock, RestockItem
database/
  migrations/      skema tabel
  seeders/         data awal (akun demo, kategori, supplier, produk)
  hobby_store.sql  dump database lengkap
resources/views/   layouts, auth, categories, suppliers, products, sales, restocks, dashboard
routes/web.php     seluruh routing
```

## Menjalankan di komputer lokal

Prasyarat: PHP 8.3+, Composer, dan MySQL (contoh: Laragon).

1. Clone repository dan masuk ke foldernya:

   ```bash
   git clone ISI_URL_REPOSITORY hobby-store
   cd hobby-store
   ```

2. Pasang dependensi:

   ```bash
   composer install
   ```

3. Salin file konfigurasi dan buat kunci aplikasi:

   ```bash
   copy .env.example .env
   php artisan key:generate
   ```

4. Buat database kosong bernama `hobby_store` (collation `utf8mb4_unicode_ci`):

   ```bash
   mysql -u root -e "CREATE DATABASE hobby_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```

5. Sesuaikan bagian `DB_` di file `.env` (host, port, username, password MySQL kamu).

6. Isi database. Pilih salah satu:

   - Lewat migration dan seeder (disarankan):

     ```bash
     php artisan migrate --seed
     ```

   - Atau impor dump SQL:

     ```bash
     mysql -u root hobby_store < database/hobby_store.sql
     ```

7. Aktifkan akses ke foto produk dan jalankan aplikasi:

   ```bash
   php artisan storage:link
   php artisan serve
   ```

8. Buka http://127.0.0.1:8000 dan login dengan akun demo di atas.

## Konfigurasi environment

Semua nilai rahasia (kunci aplikasi, kredensial database) disimpan di `.env`, yang **tidak** ikut Git. Repository hanya berisi `.env.example` sebagai contoh.

## Deployment

Aplikasi online berjalan di **Wasmer Edge** dengan database MySQL terkelola.

- `wasmer.toml` mendefinisikan runtime PHP 8.3 dan perintah menjalankan aplikasi
- `app.yaml` berisi konfigurasi aplikasi dan variabel non-rahasia
- `APP_KEY` disimpan sebagai secret (`wasmer app secrets create`), tidak pernah ada di file maupun di Git
- Kredensial database dibuat dan disuntikkan oleh platform (`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USERNAME`, `DB_PASSWORD`)
- Database online diisi dari `database/hobby_store.sql`
- Di production: `APP_ENV=production`, `APP_DEBUG=false`, cookie session hanya lewat HTTPS

Langkah ringkas deploy ulang:

```bash
git clone ISI_URL_REPOSITORY hobby-store-deploy
cd hobby-store-deploy
composer install --no-dev --optimize-autoloader
rmdir /s /q .git
wasmer deploy
```