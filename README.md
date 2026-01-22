# UMKM Serua  
**Sistem Perizinan UMKM Berbasis Web dengan Konsep Blockchain-inspired Audit Trail**

Aplikasi web berbasis **PHP & MySQL** yang dikembangkan untuk mendukung  
**transparansi, akuntabilitas, dan efisiensi proses perizinan UMKM**  
di **Kelurahan Serua, Ciputat**, dengan pendekatan **Design Thinking**  
dan konsep **Blockchain-inspired Audit Trail**.

---

## 📌 Latar Belakang
Proses perizinan UMKM di tingkat kelurahan masih banyak dilakukan secara manual
sehingga berpotensi menimbulkan permasalahan seperti keterlambatan proses,
kurangnya transparansi status perizinan, serta tidak tersedianya jejak perubahan data.

Oleh karena itu, dikembangkan sebuah sistem informasi berbasis web
yang mampu memberikan kemudahan akses, transparansi proses,
serta pencatatan histori perubahan data secara terstruktur.

---

## 🎯 Tujuan Pengembangan
- Digitalisasi proses pengajuan dan pengelolaan perizinan UMKM
- Meningkatkan transparansi status perizinan
- Menyediakan histori perubahan data (audit trail)
- Meningkatkan kualitas layanan publik di tingkat kelurahan

---

## 🧩 Ruang Lingkup Sistem
- Pengelolaan data pelaku UMKM
- Pengajuan perizinan UMKM secara online
- Verifikasi dan validasi data oleh admin
- Monitoring status perizinan
- Pencatatan histori perubahan data berbasis hash

---

## 🔐 Konsep Blockchain-inspired Audit Trail
Sistem ini tidak menggunakan blockchain publik, namun mengadopsi prinsip dasar blockchain,
antara lain:
- Hashing data menggunakan algoritma kriptografi
- Rantai histori perubahan data (linked records)
- Timestamp pada setiap perubahan data
- Data tidak dapat diubah tanpa meninggalkan jejak perubahan

Implementasi konsep ini bertujuan untuk menjaga integritas dan transparansi data perizinan.

---

## 👥 Hak Akses Pengguna
### 1️⃣ Admin
- Login sistem
- Verifikasi dan validasi perizinan UMKM
- Melihat histori perubahan data (audit trail)
- Mengelola data pengguna dan UMKM

### 2️⃣ Warga / Pelaku UMKM
- Registrasi dan login akun
- Mengajukan perizinan UMKM
- Melihat status permohonan perizinan
- Monitoring proses perizinan


## 🗂️ Struktur Direktori
umkm/
├── assets/ # File pendukung (CSS, JavaScript, gambar)
├── config/ # Konfigurasi aplikasi & database
├── controls/ # Logic aplikasi (controller / proses)
├── public/ # Halaman publik (frontend)
├── uploads/ # Penyimpanan dokumen UMKM
├── vendor/ # Library pihak ketiga
│
├── blockchain_detail.php # Detail histori perubahan data (audit trail)
├── blockchain-list.php # Daftar rantai histori berbasis hash
├── hash.php # Fungsi hashing data
│
├── index.html # Halaman utama aplikasi
├── login.php # Autentikasi pengguna
├── register.php # Registrasi akun UMKM
│
├── umkm.sql # Struktur database
├── README.md # Dokumentasi proyek


## ⚙️ Spesifikasi Sistem
### Perangkat Lunak
- PHP Native
- MySQL
- Apache Web Server (Laragon)
- Bootstrap
- phpMyAdmin

### Perangkat Keras Minimal
- Processor Dual Core
- RAM 4 GB
- Penyimpanan 20 GB
- Sistem Operasi Windows / Linux

---

## 🛠️ Cara Instalasi
1. Clone repository atau download project
2. Pindahkan folder ke direktori web server
   C:\laragon\www\
4. Import database:
- Buka phpMyAdmin
- Import file `umkm.sql`
4. Konfigurasi koneksi database pada:
config/koneksi.php


## 📝 Riwayat Pengembangan
### 📅 Januari 2026
- Perancangan struktur aplikasi
- Implementasi sistem perizinan UMKM
- Penerapan konsep blockchain-inspired audit trail
- Rapihkan Frontend
- Minus Print Surat Legalisasi Belum

  
## 📄 Lisensi
Proyek ini dibuat untuk keperluan **akademik dan edukasi**.
- Pengujian sistem
- Dokumentasi aplikasi (README)
