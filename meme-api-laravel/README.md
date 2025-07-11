# Backend API - Forum Meme

Direktori ini berisi seluruh kode sumber untuk **RESTful API** Proyek Forum Meme, yang dibangun menggunakan **Laravel**.

## Dokumentasi Utama

Untuk dokumentasi lengkap mengenai fitur, arsitektur, dan instruksi setup untuk keseluruhan proyek (backend & frontend), silakan lihat file [**README.md utama di direktori root**](../README.md).

## Setup Cepat (Backend)

1.  Pastikan Anda berada di direktori ini.
2.  Salin file `.env.example` menjadi `.env` dan atur koneksi database.
3.  Jalankan perintah berikut:
    ```bash
    composer install
    php artisan key:generate
    php artisan migrate:fresh --seed
    php artisan storage:link
    php artisan serve
    ```
