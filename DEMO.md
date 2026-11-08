# Panduan Skenario Demo UAS — Aplikasi Perpustakaan Digital Kampus

Dokumen ini merupakan panduan skenario demo langsung (*live demo*) untuk evaluasi akhir semester (UAS) mata kuliah Pemrograman Framework. Skenario disusun secara runtut dan sistematis sesuai dengan format evaluasi pada modul praktikum.

---

## 📋 Checklist Persiapan Sebelum Demo (*Pre-flight Check*)

Pastikan seluruh poin berikut sudah siap sebelum memulai sesi demo di depan dosen penguji:

- [ ] **Database sudah bersih & terisi:** Jalankan perintah `php artisan migrate:fresh --seed` di terminal.
- [ ] **Dua server telah berjalan bersamaan:**
  - **Terminal 1 (Web Server):** `php artisan serve --port=8000`
  - **Terminal 2 (Internal API Server):** `php artisan serve --port=8011`
- [ ] **Aplikasi Web siap di browser:** Buka tab di `http://127.0.0.1:8000/login`.
- [ ] **Postman siap dibuka:** Siapkan workspace/collection pengujian API untuk `http://127.0.0.1:8000/api` atau `http://127.0.0.1:8011/api`.
- [ ] **Kredensial Login dihafal:**
  - **Admin:** `admin@pens.ac.id` | Password: `password` (Nama: Bambang Sutrisno)
  - **Petugas:** `petugas1@pens.ac.id` | Password: `password` (Nama: Siti Rahmawati)

> ⚠️ **Catatan Penting Dua Server:** Halaman **Dashboard** (`/`) dan **Laporan Peminjaman** (`/loans/report`) mengonsumsi API internal melalui HTTP Client (`Http::get()`). Karena PHP built-in web server (`php artisan serve`) bersifat *single-threaded*, server kedua di port `8011` **wajib menyala** agar tidak terjadi *deadlock* (request menggantung).

---

## 🎬 Urutan Langkah Skenario Demo Live

Ikuti 10 langkah demonstrasi berikut secara berurutan:

### Langkah 1: Login ke Aplikasi
1. Buka browser dan arahkan ke `http://127.0.0.1:8000/login`.
2. Masukkan kredensial akun **Admin**:
   - **Email:** `admin@pens.ac.id`
   - **Password:** `password`
3. Klik tombol **Login**.
4. **Hal yang Ditunjukkan ke Dosen:**
   - Redirect otomatis menuju halaman **Dashboard** (`/`), bukan lagi ke `/books`.
   - Muncul alert flash message sukses: *"Login berhasil, selamat datang Bambang Sutrisno."*
   - Di navbar pojok kanan atas tampil nama dan role pengguna: `Bambang Sutrisno (Admin)`.
   - Menu **Kategori** muncul di navbar karena login sebagai admin (otorisasi middleware `CheckAdminRole`).

---

### Langkah 2: Tampilkan Dashboard & Jelaskan Konsumsi API Internal
1. Perhatikan tampilan 3 kartu statistik di halaman `/`:
   - **Total Buku** (misal: 20)
   - **Total Anggota** (misal: 15)
   - **Peminjaman Aktif** (misal: jumlah transaksi status `dipinjam`)
2. **Penjelasan Konseptual ke Dosen:**
   > *"Data pada ketiga kartu statistik ini tidak di-query langsung menggunakan Model Eloquent di `DashboardController`, melainkan dikonsumsi dari endpoint API internal `GET /api/stats` pada port 8011 menggunakan Laravel HTTP Client (`Http::get()`). Controller juga dilengkapi `try-catch ConnectionException` sebagai fallback (angka 0) apabila server API mati."*

---

### Langkah 3: Tambah Kategori Baru (Fitur Khusus Admin)
1. Klik menu **Kategori** di navbar (`http://127.0.0.1:8000/categories`).
2. Klik tombol **+ Tambah Kategori**.
3. Isi form:
   - **Nama Kategori:** `Ensiklopedia`
   - **Deskripsi:** `Buku referensi lengkap memuat fakta dan ilmu pengetahuan umum.`
4. Klik **Simpan**.
5. **Hal yang Ditunjukkan ke Dosen:**
   - Kategori baru tersimpan dan langsung muncul di tabel daftar kategori.
   - Muncul flash message sukses penyimpanan.

---

### Langkah 4: Tambah Buku dengan Relasi Kategori
1. Klik menu **Buku** di navbar (`http://127.0.0.1:8000/books`).
2. Klik tombol **+ Tambah Buku**.
3. Isi form tambah buku:
   - **Judul:** `Ensiklopedia Sains Nusantara`
   - **Penulis:** `Prof. Ahmad Santoso`
   - **Penerbit:** `PENS Press`
   - **Tahun Terbit:** `2024`
   - **ISBN:** `978-602-1234-56-7`
   - **Stok:** `5`
   - **Kategori:** Pilih kategori yang baru dibuat di Langkah 3 (`Ensiklopedia`).
4. Klik **Simpan**.
5. **Hal yang Ditunjukkan ke Dosen:**
   - Buku baru tampil di tabel katalog buku.
   - Kolom kategori menampilkan nama kategori asli (`Ensiklopedia`), bukan ID mentah (`category_id`), membuktikan relasi `belongsTo` berjalan baik.

---

### Langkah 5: Tambah Anggota Baru & Uji Validasi
1. Klik menu **Anggota** di navbar (`http://127.0.0.1:8000/members`).
2. Klik tombol **+ Tambah Anggota**.
3. Isi form anggota baru:
   - **Nama:** `Farhan Pratama`
   - **NIM:** `3125600099`
   - **Email:** `farhan.pratama@pens.ac.id`
   - **Nomor Telepon:** `081234567899`
   - **Alamat:** `Jl. Raya ITS Kampus PENS Sukolilo Surabaya`
   - **Status:** `aktif`
4. Klik **Simpan**.
5. *(Opsional pengujian validasi):* Buka form tambah lagi dan coba submit dengan NIM atau email yang sama persis — tunjukkan validasi unik server-side menolak data duplikat dengan pesan error ramah pengguna.
6. **Hal yang Ditunjukkan ke Dosen:**
   - Anggota baru tersimpan di database dan muncul pada tabel daftar anggota.

---

### Langkah 6: Buat Transaksi Peminjaman Baru
1. Klik menu **Peminjaman** di navbar (`http://127.0.0.1:8000/loans`).
2. Klik tombol **+ Tambah Peminjaman** (`/loans/create`).
3. Isi data formulir:
   - **Anggota:** Pilih anggota yang baru dibuat (`Farhan Pratama`).
   - **Tanggal Pinjam:** Tanggal hari ini.
   - **Tanggal Kembali:** 7 hari dari hari ini.
   - **Buku:** Centang buku yang baru dibuat (`Ensiklopedia Sains Nusantara`).
4. Klik **Simpan Transaksi**.
5. **Hal yang Ditunjukkan ke Dosen:**
   - Field petugas (`user_id`) otomatis diisi dari `auth()->id()` user yang sedang aktif tanpa perlu diinput manual di form.
   - Pivot table relasi many-to-many (`loan_items`) terisi otomatis.

---

### Langkah 7: Lihat Daftar Peminjaman Aktif & Eager Loading
1. Aplikasi otomatis redirect ke halaman `/loans`.
2. Periksa baris transaksi peminjaman yang baru saja dibuat.
3. **Hal yang Ditunjukkan ke Dosen:**
   - Transaksi tampil dengan badge status **`Dipinjam`** (berwarna kuning/oranye).
   - Nama peminjam (`Farhan Pratama`), nama petugas pencatat (`Bambang Sutrisno`), dan judul buku tampil lengkap.
   - **Poin Nilai Tambah:** Jelaskan bahwa query di `LoanController@index` menerapkan *Eager Loading* (`Loan::with(['member', 'user', 'loanItems.book'])`) untuk mencegah *N+1 Query Problem*.

---

### Langkah 8: Proses Pengembalian Buku
1. Pada tabel peminjaman di baris transaksi yang baru dibuat, klik tombol **Kembalikan**.
2. Konfirmasi dialog konfirmasi pada browser.
3. **Hal yang Ditunjukkan ke Dosen:**
   - Status peminjaman langsung terupdate menjadi **`Dikembalikan`** (badge hijau).
   - Tanggal pengembalian (`tanggal_dikembalikan`) tercatat otomatis sesuai tanggal saat tombol ditekan.
   - Tombol "Kembalikan" otomatis hilang karena buku sudah tidak lagi berstatus `dipinjam`.
   - *(Jika diminta pengujian lewat API):* Tunjukkan endpoint `PUT /api/loans/{id}/kembalikan` di Postman yang menghasilkan response JSON dengan status `dikembalikan`.

---

### Langkah 9: Tampilkan Halaman Laporan Peminjaman
1. Klik menu **Laporan** di navbar atau akses URL `http://127.0.0.1:8000/loans/report`.
2. Tunjukkan tabel data laporan peminjaman yang memuat riwayat transaksi, termasuk transaksi yang baru saja dikembalikan.
3. **Penjelasan Konseptual ke Dosen:**
   > *"Halaman Laporan ini mengambil data murni dari endpoint REST API `GET /api/loans` (port 8011). Controller mem-parsing response JSON array dan menyajikan navigasi paginasinya secara manual dari objek `meta`, tanpa bergantung pada Query Builder Eloquent di controller web."*

---

### Langkah 10: Pengujian REST API Langsung di Postman
Buka aplikasi **Postman** dan jalankan pengujian endpoint berikut secara live:

1. **Uji Endpoint `GET /api/books`:**
   - **URL:** `http://127.0.0.1:8000/api/books` (atau port 8011)
   - **Header:** `Accept: application/json`
   - **Hasil:** Response HTTP 200 OK dengan payload JSON terstruktur rapi lewat `BookResource`, lengkap dengan relasi objek `category` dan metadata pagination.
2. **Uji Endpoint `GET /api/stats`:**
   - **URL:** `http://127.0.0.1:8000/api/stats`
   - **Header:** `Accept: application/json`
   - **Hasil:** Response HTTP 200 OK berisi JSON `{ "total_buku": X, "total_anggota": Y, "peminjaman_aktif": Z }` yang angkanya sinkron dengan database terbaru.
3. **Uji Endpoint Detail/CRUD API Lainnya *(jika diminta)*:**
   - `GET /api/books/{id}` (Detail buku)
   - `POST /api/books` (Tambah buku via JSON payload)
   - `GET /api/loans` (Daftar transaksi peminjaman)

---

## 💡 Panduan Cepat Tanya Jawab Konseptual UAS

Berikut rangkuman jawaban untuk 13 pertanyaan konseptual yang sering diajukan dosen penguji:

| No | Pertanyaan | Poin Kunci Jawaban |
|:--:|---|---|
| **1** | **Masalah jika tanpa framework & kaitan dengan MVC?** | Tanpa framework, kode PHP native rentan mencampur logika bisnis, query SQL, dan tampilan HTML dalam satu file (*spaghetti code*). Arsitektur MVC memisahkan tanggung jawab: **Model** (data & aturan bisnis), **View** (antarmuka/Blade), dan **Controller** (penghubung & pengatur alur), sehingga kode terstruktur, mudah diuji, dan aman dikembangkan bersama tim. |
| **2** | **Alur Request Lifecycle submit form & posisi Middleware?** | Browser submit `POST /books` ➔ Masuk `public/index.php` ➔ Bootstrap framework ➔ Routing via `routes/web.php` ➔ **Middleware** dieksekusi (memeriksa sesi `auth` & token CSRF) ➔ Jika lolos, diarahkan ke `BookController@store` ➔ Validasi input dijalankan ➔ Simpan ke database via Eloquent `Book::create()` ➔ Redirect response dengan flash session ke `books.index` ➔ Browser merender view Blade dengan data terbaru. |
| **3** | **Mengapa validasi server-side wajib meski ada `required` di HTML?** | Validasi HTML (client-side) sangat mudah dimatikan atau dilewati (lewat inspect element browser, cURL, atau Postman). Validasi di sisi server (controller) adalah benteng pertahanan utama untuk menjamin integritas data dan mencegah injeksi/data cacat masuk ke database. |
| **4** | **Masalah yang diselesaikan Migration dalam tim?** | Migration bertindak sebagai *version control system* untuk skema database. Setiap anggota tim dapat mereplikasi struktur tabel yang identik secara otomatis tanpa perlu ekspor-impor file `.sql` manual yang rawan konflik, inkonsistensi versi, dan *human error*. |
| **5** | **N+1 Query Problem & solusi Eager Loading?** | N+1 terjadi jika kita mengambil $N$ data peminjaman (1 query), lalu saat looping memanggil `$loan->member->nama` di Blade, Eloquent melakukan 1 query tambahan untuk setiap baris peminjaman ($1 + N$ query). Eager loading (`Loan::with(['member', 'user'])`) menyelesaikan masalah ini dengan hanya mengeksekusi 2-3 query saja memakai klausa SQL `WHERE IN (...)`. |
| **6** | **Beda relasi Eloquent vs Foreign Key di Migration?** | Foreign key di migration bekerja di level mesin database RDBMS untuk menegakkan integritas referensial data (mencegah data *orphan* atau salah referensi). Relasi Eloquent (`hasMany`/`belongsTo`) bekerja di level kode PHP aplikasi untuk mempermudah navigasi data antar model berorientasi objek. Keduanya saling melengkapi. |
| **7** | **Beda Autentikasi vs Otorisasi & penerapannya?** | **Autentikasi** membuktikan *siapa Anda* (verifikasi identitas, diterapkan di `AuthController` login/logout). **Otorisasi** menentukan *hak akses apa yang Anda miliki* (diterapkan di middleware `CheckAdminRole` yang membatasi akses modul Kategori hanya untuk role `admin`). |
| **8** | **Kenapa REST API mengembalikan JSON bukan HTML?** | JSON adalah format pertukaran data netral, ringan, dan standar industri (*language-agnostic*). JSON dapat dikonsumsi oleh berbagai macam klien tanpa terikat tampilan: mobile app (Flutter/React Native), SPA (React/Vue), aplikasi pihak ketiga, maupun microservices lain. |
| **9** | **Fungsi API Resource vs `response()->json()` langsung?** | API Resource bertindak sebagai lapisan transformasi (*transformation layer*). Mengontrol field mana saja yang boleh diekspos (menyembunyikan field sensitif), menyeragamkan struktur payload JSON, memformat tanggal, dan memisahkan representasi data API dari struktur tabel database fisik. |
| **10** | **Kenapa butuh 2 server di `php artisan serve` & apakah terjadi di produksi?** | `php artisan serve` (PHP built-in CLI server) bersifat *single-threaded* (hanya memproses 1 request dalam satu waktu). Saat Controller memanggil API ke port yang sama, request kedua tidak bisa diproses karena server masih menunggu respon request pertama (*deadlock*). Di server produksi (Nginx + PHP-FPM / Octane), masalah ini tidak terjadi karena web server produksi menangani *multi-process/multi-threading* secara serentak. |
| **11** | **Fungsi Seeder & Factory serta alasan pemisahannya?** | **Seeder** bertugas mengatur eksekusi pengisian data ke tabel database (sering kali data master tetap seperti kategori & user admin). **Factory** bertugas merancang pola dan membuat data acak dalam jumlah banyak menggunakan Faker. Keduanya dipisah mengikuti *Single Responsibility Principle*: Factory mendefinisikan *cetak biru data*, Seeder mengatur *orkestrasi pengisiannya*. |
| **12** | **Alur pembuatan fitur baru dari awal (contoh: `publishers`)?** | 1. `php artisan make:model Publisher -m` (buat Model & Migration) ➔ 2. Rancang skema tabel di file migration & jalankan `php artisan migrate` ➔ 3. Definisikan `$fillable` dan relasi di Model ➔ 4. Buat Form Request untuk validasi ➔ 5. Buat Controller resource (`PublisherController`) ➔ 6. Daftarkan route di `routes/web.php` ➔ 7. Buat view Blade (index, create, edit) yang meng-extend master layout. |
| **13** | **Alasan 2 branch (`dev`/`main`) & commit bertahap (`[P-1]` s.d `[P-10]`)?** | Branch `main` dijaga tetap stabil (*production-ready*), sedangkan pengembangan eksperimental dilakukan di branch `dev`. Commit bertahap per pertemuan memberikan riwayat pelacakan perubahan yang transparan, mempermudah identifikasi *bug* (*bisecting*), serta membuktikan proses pembelajaran yang otentik dan konsisten. |
