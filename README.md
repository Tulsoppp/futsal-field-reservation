# Jaya Futsal - Sistem Reservasi Lapangan

Project Laravel 12 untuk sistem reservasi futsal dengan dua sisi tampilan:

- User: landing page, login, register, reservasi, dan membership.
- Admin: dashboard, kelola jadwal, kelola reservasi, laporan, membership, dan pelanggan.

## Struktur Utama

- Blade layouts dan partials: `resources/views/layouts` dan `resources/views/partials`
- Halaman user: `resources/views/pages/user`
- Halaman admin: `resources/views/pages/admin`
- Aset statis: `public/assets`

## Menjalankan Project (Local)

1. Buat file `.env` dari `.env.example` bila belum ada.
2. Generate app key:
    ```
    php artisan key:generate
    ```
3. Jalankan server:
    ```
    php artisan serve
    ```

## Catatan

- Saat ini halaman masih bersifat statis melalui `Route::view` di `routes/web.php`.
- Integrasi database dan controller bisa ditambahkan setelah kebutuhan data siap.
