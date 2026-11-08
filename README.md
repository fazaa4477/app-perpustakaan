# 📚 Sistem Informasi Perpustakaan Digital Kampus

Aplikasi web manajemen perpustakaan modern berbasis **Laravel 12** yang dibangun secara bertahap dari Pertemuan 1 hingga Pertemuan 10 dalam mata kuliah Pemrograman Framework. Aplikasi ini mengintegrasikan arsitektur Model-View-Controller (MVC), autentikasi berbasis sesi, otorisasi peran (*role-based authorization*), manajemen basis data relasional dengan *Eager Loading*, RESTful API terstandarisasi dengan *API Resource*, serta implementasi *Backend for Frontend* (BFF) melalui konsumsi API internal menggunakan *Laravel HTTP Client*.

---

## 🚀 Fitur Utama Aplikasi

- **Autentikasi & Otorisasi:**
  - Login dan logout berbasis session dengan validasi kredensial aman (`bcrypt`).
  - *Role-Based Access Control* (RBAC) menggunakan custom middleware:
    - **Admin:** Memiliki akses penuh terhadap seluruh modul, termasuk pengelolaan data master Kategori (`/categories`).
    - **Petugas:** Mengelola transaksi peminjaman, data buku, dan data anggota.
- **Manajemen Master Data (CRUD Lengkap):**
  - **Buku (`/books`):** Kelola katalog buku dengan relasi kategori (`belongsTo`), ISBN unik, stok, dan pencarian/filter.
  - **Kategori (`/categories`):** Kelola kategori buku khusus hak akses administrator.
  - **Anggota (`/members`):** Kelola data mahasiswa anggota perpustakaan dengan validasi unik NIM dan email kampus.
- **Transaksi Peminjaman & Pengembalian (`/loans`):**
  - Pencatatan transaksi peminjaman multi-buku (*many-to-many relationship* via pivot table `loan_items`).
  - Petugas pencatat (`user_id`) otomatis terisi dari user yang sedang login (`auth()->id()`).
  - Tombol aksi pengembalian buku sekali klik (*single-action route*) yang otomatis mencatat tanggal pengembalian dan mengubah status transaksi.
  - Tampilan status dengan badge visual dinamis (`dipinjam`, `dikembalikan`, `terlambat`).
  - Optimasi query database dengan *Eager Loading* untuk eliminasi *N+1 Query Problem*.
- **Dashboard Statistik & Laporan Peminjaman (Internal API Consumption):**
  - **Dashboard (`/`):** Menyajikan ringkasan metrik statistik secara *real-time* (Total Buku, Total Anggota, Peminjaman Aktif) yang dikonsumsi dari endpoint `GET /api/stats` via Laravel HTTP Client (`Http::get()`).
  - **Laporan Peminjaman (`/loans/report`):** Menampilkan seluruh rekapitulasi data peminjaman yang dikonsumsi langsung dari endpoint `GET /api/loans` lengkap dengan navigasi pagination berbasis metadata API.
  - Penanganan toleransi kesalahan (*fault-tolerant*) menggunakan `try-catch ConnectionException` sebagai nilai fallback jika server API tidak dapat dijangkau.
- **RESTful API Terstandarisasi:**
  - Menyediakan 11 endpoint REST API publik (`books`, `members`, `loans`, `stats`).
  - Response konsisten berformat JSON yang ditransformasikan melalui *API Resource* (`BookResource`, `MemberResource`, `LoanResource`).
  - Penerapan HTTP status code semantik (200 OK, 201 Created, 404 Not Found, 422 Unprocessable Entity).
  - Dokumentasi API lengkap tersedia di file [`api.md`](./api.md).
- **Seeder & Factory Data Realistis:**
  - Menggunakan konfigurasi Faker berbahasa Indonesia (`APP_FAKER_LOCALE=id_ID`).
  - Kurasi manual untuk judul buku per kategori, penerbit nasional terkemuka, dan format email domain kampus (`@pens.ac.id`).
- **Tampilan Bersih & Responsif:**
  - Layout master Blade modular dengan partials navigation dan alert notifikasi.
  - Custom pagination yang ringan dan rapi tanpa ketergantungan framework CSS eksternal.

---

## 🛠️ Kebutuhan Sistem (*Prerequisites*)

Pastikan lingkungan lokal Anda memenuhi persyaratan minimum berikut:

- **PHP:** Versi `>= 8.2` (ekstensi aktif: `pdo_mysql`, `curl`, `mbstring`, `openssl`, `bcmath`, `tokenizer`)
- **Composer:** Versi `>= 2.x`
- **Database Server:** MySQL / MariaDB (versi `>= 8.0` / `>= 10.4`)
- **Git**
- **Web Browser Modern** (Chrome, Firefox, Edge)
- **Postman** (opsional, untuk demonstrasi dan pengujian endpoint REST API)

---

## 📥 Panduan Instalasi & Setup Proyek

Ikuti langkah-langkah berikut untuk meng-clone dan menyiapkan proyek dari awal:

### 1. Clone Repository
```bash
git clone https://github.com/fazaa4477/app-perpustakaan.git
cd app-perpustakaan
```

### 2. Install Dependensi PHP
Jalankan composer untuk mengunduh seluruh vendor library:
```bash
composer install
```

### 3. Konfigurasi Environment (`.env`)
Salin file template environment ke `.env`:
```bash
cp .env.example .env
```
*(Bagi pengguna Windows Command Prompt / PowerShell, gunakan `copy .env.example .env`)*

Buka file `.env` dan sesuaikan konfigurasi database dan URL API internal Anda:
```env
APP_NAME=PerpustakaanDigital
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

# Base URL server kedua khusus konsumsi API internal
INTERNAL_API_URL=http://127.0.0.1:8011

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=id_ID

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_perpustakaan
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Buat Database & Jalankan Migrasi + Seeder
Buat database baru di MySQL dengan nama `db_perpustakaan` (misalnya via phpMyAdmin atau terminal MySQL: `CREATE DATABASE db_perpustakaan;`).

Kemudian jalankan migrasi beserta seeding data dummy:
```bash
php artisan migrate:fresh --seed
```
Perintah ini akan membuat seluruh tabel skema relasional dan mengisinya dengan data acak realistis berstandar Indonesia:
- 3 akun pengguna (1 Admin, 2 Petugas)
- 5 kategori buku terkurasi
- 20 data buku lengkap dengan ISBN & penerbit
- 15 data anggota mahasiswa berdomain `@pens.ac.id`
- 10 transaksi peminjaman lengkap dengan item relasi

---

## ⚡ Cara Menjalankan Aplikasi (Wajib 2 Server)

> ⚠️ **MENGAPA MEMERLUKAN DUA SERVER?**  
> Server pengembangan bawaan PHP CLI (`php artisan serve`) hanya memproses **satu request secara berurutan (*single-threaded*)**.  
> Saat browser mengakses Dashboard (`/`) atau Laporan (`/loans/report`), controller aplikasi memanggil API internal miliknya sendiri (`Http::get()`). Jika request tersebut diarahkan ke port yang sama, server akan mengalami **deadlock** (saling tunggu dan macet permanen).  
> Solusinya adalah menjalankan **dua proses server terpisah**:
> 1. **Server Utama (Port 8000):** Menangani trafik tampilan antarmuka web di browser.
> 2. **Server API Internal (Port 8011):** Menangani panggilan data API yang diminta oleh controller web.

Buka **dua jendela terminal terpisah** di direktori proyek `app-perpustakaan`:

### 🖥️ Terminal 1 — Server Web Utama
```bash
php artisan serve --port=8000
```
Server web siap diakses pengguna di: **`http://127.0.0.1:8000`**

### 🖥️ Terminal 2 — Server API Internal
```bash
php artisan serve --port=8011
```
Server API siap melayani request internal pada: **`http://127.0.0.1:8011`**

---

## 🔑 Kredensial Login Bawaan (*Default Seeded Users*)

Seluruh akun default menggunakan password: **`password`**

| Role | Nama Pengguna | Email | Hak Akses |
|---|---|---|---|
| **Admin** | Bambang Sutrisno | `admin@pens.ac.id` | Akses penuh (Dashboard, Buku, Kategori, Anggota, Peminjaman, Laporan) |
| **Petugas 1** | Siti Rahmawati | `petugas1@pens.ac.id` | Akses operasional (Dashboard, Buku, Anggota, Peminjaman, Laporan) |
| **Petugas 2** | Ahmad Fauzi | `petugas2@pens.ac.id` | Akses operasional (Dashboard, Buku, Anggota, Peminjaman, Laporan) |

---

## 🌐 Dokumentasi REST API

Aplikasi menyediakan REST API dengan format respon JSON terstandarisasi. Daftar ringkas endpoint:

| Method | Endpoint | Keterangan |
|---|---|---|
| `GET` | `/api/books` | Mengambil katalog buku (mendukung filter `judul` & `category_id` + pagination) |
| `GET` | `/api/books/{id}` | Mengambil detail 1 buku berdasarkan ID |
| `POST` | `/api/books` | Menambahkan data buku baru |
| `PUT` | `/api/books/{id}` | Memperbarui data buku berdasarkan ID |
| `DELETE` | `/api/books/{id}` | Menghapus data buku berdasarkan ID |
| `GET` | `/api/members` | Mengambil seluruh daftar anggota (dengan pagination) |
| `GET` | `/api/members/{id}` | Mengambil detail 1 anggota berdasarkan ID |
| `GET` | `/api/loans` | Mengambil seluruh riwayat peminjaman (dengan pagination) |
| `POST` | `/api/loans` | Membuat transaksi peminjaman baru |
| `PUT` | `/api/loans/{id}/kembalikan` | Menandai buku pada transaksi telah dikembalikan |
| `GET` | `/api/stats` | Statistik jumlah buku, anggota, dan peminjaman aktif |

> 📖 Untuk payload request/response lengkap, silakan baca dokumentasi rinci di [`api.md`](./api.md).

---

## 🎓 Panduan Skenario Demo UAS

Untuk panduan urutan pengujian langsung di depan dosen penguji (*live demo*) beserta catatan tanya jawab konseptual, silakan mengacu ke dokumen:  
👉 **[`DEMO.md`](./DEMO.md)**

---

## 📁 Struktur Direktori Penting

```text
app-perpustakaan/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/                     # REST API Controllers (Books, Loans, Members, Stats)
│   │   │   ├── AuthController.php       # Controller Login & Logout
│   │   │   ├── BookController.php       # Controller CRUD Buku
│   │   │   ├── CategoryController.php   # Controller CRUD Kategori (Admin)
│   │   │   ├── DashboardController.php  # Controller Dashboard (Consume GET /api/stats)
│   │   │   ├── LoanController.php       # Controller Transaksi & Laporan (Consume GET /api/loans)
│   │   │   └── MemberController.php     # Controller CRUD Anggota
│   │   ├── Middleware/
│   │   │   └── CheckAdminRole.php       # Middleware Otorisasi Role Admin
│   │   └── Resources/                   # Eloquent API Resources (Transformasi JSON)
│   └── Models/                          # Eloquent Models (User, Book, Category, Member, Loan, LoanItem)
├── config/
│   └── services.php                     # Konfigurasi internal_api.base_url
├── database/
│   ├── factories/                       # BookFactory, MemberFactory (Faker ID)
│   ├── migrations/                      # Skema tabel database perpustakaan
│   └── seeders/                         # DatabaseSeeder, CategorySeeder, UserSeeder, dll.
├── resources/
│   └── views/
│       ├── layouts/app.blade.php        # Layout induk Blade
│       ├── partials/                    # Navbar & Alert partials
│       ├── dashboard.blade.php          # Tampilan Dashboard statistik
│       ├── books/                       # View CRUD Buku
│       ├── categories/                  # View CRUD Kategori
│       ├── members/                     # View CRUD Anggota
│       ├── loans/                       # View Transaksi & Laporan Peminjaman
│       └── vendor/pagination/custom.blade.php  # View Custom Pagination
├── routes/
│   ├── web.php                          # Rute Antarmuka Web (Session-protected)
│   └── api.php                          # Rute REST API Publik
├── api.md                               # Dokumentasi spesifikasi REST API
├── DEMO.md                              # Skenario pengujian & tanya jawab UAS
└── README.md                            # Panduan utama proyek
```

---

## 📄 Lisensi

Proyek ini dikembangkan untuk kebutuhan praktikum akademik mata kuliah Pemrograman Framework menggunakan open-source framework [Laravel](https://laravel.com) di bawah lisensi [MIT license](https://opensource.org/licenses/MIT).
