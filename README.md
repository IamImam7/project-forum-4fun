<div align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/Vue.js-35495E?style=for-the-badge&logo=vue.js&logoColor=4FC08D" alt="Vue.js">
  <img src="https://img.shields.io/badge/Vite-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite">
  <img src="https://img.shields.io/badge/Pinia-FFB300?style=for-the-badge&logo=pinia&logoColor=white" alt="Pinia">
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
</div>
# 🚀 4Fun - Forum Meme "Retro Vaporwave"

Selamat datang di 4Fun! Ini adalah aplikasi web full-stack yang dibangun dari nol untuk menciptakan sebuah platform komunitas berbagi meme yang modern, interaktif, dan penuh gaya.

Proyek ini menggunakan arsitektur *decoupled*, dengan **Laravel** sebagai API backend yang tangguh dan **Vue.js 3** sebagai Single Page Application (SPA) yang dinamis di frontend.
<img width="1920" height="2391" alt="Image" src="https://github.com/user-attachments/assets/3039dc97-0720-40c1-a79a-8080dc261a58" />

---
## ✨ Fitur-Fitur Utama

Aplikasi ini dilengkapi dengan berbagai fitur untuk menciptakan pengalaman pengguna yang lengkap dan menyenangkan.

#### 👤 **Sistem Pengguna & Akun**
* **Otentikasi Lengkap**: Sistem registrasi, login, dan logout yang aman menggunakan **Laravel Sanctum**.
* **Halaman Profil Pribadi**: Pengguna dapat melihat semua postingan yang pernah mereka buat di halaman "Postingan Saya".
* **Edit Profil**: Kemampuan bagi pengguna untuk mengubah nama, email, dan kata sandi mereka.
* **Profil Publik**: Setiap nama pengguna di aplikasi bisa diklik untuk melihat galeri postingan dari pengguna tersebut.
* **Menu Dropdown Profil**: Navbar yang menampilkan nama pengguna dan menu dropdown untuk akses cepat ke edit profil dan logout.

#### 🖼️ **Postingan & Interaksi**
* **Upload Meme**: Form upload modern dengan preview gambar, opsi multi-kategori, dan tombol hapus pilihan gambar.
* **Sistem Vote Interaktif**: Fitur *upvote* & *downvote* pada postingan dan komentar yang mengubah skor secara instan tanpa perlu me-refresh halaman (*Optimistic Update*).
* **Sistem Komentar Berantai**: Pengguna dapat berkomentar pada postingan dan membalas komentar lain (*nested comments*).
* **Hapus Postingan**: Pemilik postingan (atau admin) dapat menghapus postingan melalui halaman detail.
* **Peringatan Konten**: Komentar dengan 5 *dislike* atau lebih akan disembunyikan di balik peringatan, memberikan opsi kepada pengguna untuk tetap melihatnya.

#### 🌐 **Penemuan Konten**
* **Sorting Dinamis**: Urutkan feed utama berdasarkan **New** (terbaru), **Hot** (trending saat ini), atau **Top** (skor tertinggi sepanjang masa).
* **Halaman per Kategori**: Setiap tag kategori dapat diklik untuk menjelajahi semua meme dalam kategori tersebut.
* **Fungsi Pencarian**: *Search bar* di navbar untuk mencari meme berdasarkan judul.
* **Pagination**: Navigasi halaman yang mudah digunakan di semua halaman daftar postingan.

#### 🎨 **Desain & Pengalaman Pengguna (UX)**
* **Tema Kustom "Retro Vaporwave"**: Skema warna gelap dengan aksen neon yang konsisten di seluruh aplikasi.
* **Font Kustom**: Menggunakan font **"Audiowide"** dari Google Fonts untuk memperkuat identitas visual.
* **Animasi Loading**: Indikator *loading* statis diganti dengan animasi gradien gelombang yang keren.
* **Notifikasi Modern**: Semua umpan balik (sukses/error) menggunakan notifikasi *toast* yang elegan dan tidak mengganggu.
* **Modal Konfirmasi Kustom**: Kotak konfirmasi `alert()` bawaan browser diganti dengan modal kustom dengan efek latar belakang buram.

---
## 💻 Teknologi yang Digunakan

### **Backend (API)**
* **Laravel 10+**
* **Laravel Sanctum**
* **Eloquent ORM** 
* **Laravel Policy** (untuk otorisasi)
* **File Storage System**
* **Database Seeder & Migration**

### **Frontend (SPA)**
* **Vue.js 3** (Composition API & `<script setup>`)
* **Vite**
* **Vue Router**
* **Pinia** (State Management)
* **Axios**
* **Vue Toastification**
* **CSS3** (Flexbox, Grid, Custom Properties, Animations)

---
## 🔧 Instalasi & Setup Lokal

Ingin menjalankan proyek ini di komputer Anda? Ikuti langkah-langkah berikut:

### **Backend (Direktori Laravel)**
1.  Masuk ke direktori backend: `cd nama-folder-backend`
2.  Install dependensi:
    ```bash
    composer install
    ```
3.  Salin file environment: `cp .env.example .env`
4.  Atur koneksi database Anda di file `.env`.
5.  Generate application key:
    ```bash
    php artisan key:generate
    ```
6.  Jalankan migrasi dan seeder untuk membuat tabel dan mengisi data awal:
    ```bash
    php artisan migrate:fresh --seed
    ```
7.  Buat symbolic link untuk storage:
    ```bash
    php artisan storage:link
    ```
8.  Jalankan server pengembangan:
    ```bash
    php artisan serve
    ```

### **Frontend (Direktori Vue)**
1.  Masuk ke direktori frontend: `cd nama-folder-frontend`
2.  Install dependensi:
    ```bash
    npm install
    ```
3.  Buat file environment: `cp .env.example .env` (jika ada) atau buat file `.env` baru.
4.  Isi file `.env` dengan URL backend Anda:
    ```
    VITE_API_URL=[http://127.0.0.1:8000](http://127.0.0.1:8000)
    ```
5.  Jalankan server pengembangan:
    ```bash
    npm run dev
    ```
