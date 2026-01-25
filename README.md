UMKM Serua

Sistem Perizinan UMKM Berbasis Web (Blockchain-inspired Audit Trail)

Aplikasi web berbasis PHP & MySQL untuk mendukung transparansi, akuntabilitas, dan efisiensi proses perizinan UMKM di Kelurahan Serua, Ciputat.
Dikembangkan dengan pendekatan Design Thinking dan konsep Blockchain-inspired Audit Trail.

🎯 Tujuan

- Digitalisasi proses perizinan UMKM
- Transparansi status pengajuan
- Pencatatan histori perubahan data (audit trail)
- Peningkatan kualitas layanan publik kelurahan

🔐 Blockchain-inspired Audit Trail

Sistem tidak menggunakan blockchain publik, tetapi mengadopsi prinsip dasarnya:
- Hashing data
- Rantai histori perubahan (linked records)
- Timestamp setiap perubahan
- Perubahan data selalu meninggalkan jejak

👥 Hak Akses

👤 Admin

- Kelola data pengguna & UMKM
- Monitoring seluruh proses perizinan
- Melihat histori perubahan data (audit trail)

🏘️ Admin RT

- Verifikasi awal pengajuan UMKM di wilayah RT
- Memberikan persetujuan / penolakan
- Melihat status UMKM RT terkait

🏢 Admin RW

- Verifikasi lanjutan setelah RT
- Persetujuan / penolakan tingkat RW
- Monitoring UMKM di wilayah RW

🧑‍💼 Warga / Pelaku UMKM

- Registrasi & login
- Pengajuan perizinan UMKM

  Admin :
  username : admin, password : admin123
  RT :
  username : asep, password : 123
  RW :
  username : adjie, password : 123
  Warga
  username : topik, password : 123

🗂️ Struktur Singkat
umkm/
├── assets/
├── config/
├── controls/
├── public/
├── uploads/
├── blockchain_list.php
├── blockchain_detail.php
├── hash.php
├── umkm.sql
└── README.md

⚙️ Teknologi

- PHP Native
- MySQL
- Bootstrap
- Apache (Laragon)
- phpMyAdmin

🛠️ Instalasi Singkat

- Clone / download project
- Pindahkan ke C:\laragon\www\
- Import umkm.sql via phpMyAdmin
- Atur koneksi database di config/

🆕 Update Pengembangan (Januari 2026)

Update Terbaru:
- Penyempurnaan alur persetujuan UMKM (Admin → RT → RW)
- Penyesuaian struktur role (Admin / RT / RW tanpa tabel users)
- Perbaikan validasi form & keamanan PHP 8.1+
- Implementasi audit trail berbasis hash lebih konsisten
- Perapihan frontend & UI dashboard admin
- Perbaikan bug edit data & deprecated warning PHP

Dalam Progres:
- Cetak surat legalisasi UMKM
- Optimalisasi UX warga
- Dokumentasi teknis lanjutan

📄 Lisensi

Proyek ini dibuat untuk keperluan akademik dan edukasi.


atau versi README super singkat (1 layar GitHub)

atau disesuaikan buat proposal skripsi / laporan akhir
