## Harbinger

> The name's doesn't matter.

Prasyarat untuk running project:

- PHP & Composer
- Docker (running Pgsql, MariaDb, Mysql)

### Setup Guide

Paling mudah menggunakan Docker dengan asumsi PHP dan Composer terinstall. Bisa jalankan perintah berikut:

1. Copy `.env.example` ke `.env`
2. Jalankan `docker compose up -d` (ini akan serving 3 database: pgsql, mariadb, dan mysql)
3. Jalankan `php artisan app:arithmetic {mysql|pgsql|mariadb}`

Untuk melihat database, bisa akses adminer di http://localhost:8080. Masukkan kredensial sesuai dengan `.env`, untuk
Host sesuaikan dengan nama service di docker-compose (e.g. pgsql|mariadb|mysql).

### Penjelasan

Begini alur POC yang dilakukan:

1. Migrate Database: nama table `demo` dengan field `code`, `name` dan `price`
2. Seeders: menunjukkan cara menyimpan database dan dapat dilihat melalui `adminer`
3. Command: `app:arithmetic` menjalankan proses operasi aritmatika terhadap field `price` yang berukuran 2048 dan 4096 bit
4. Proses aritmatika menggunakan GMP yang sudah teroptimasi untuk melakukan _bit-operations_ termasuk _carrying_ yang efisien

Penyimpanan ke database dalam bentuk VARCHAR karena bahasa lain bisa secara kompatibel menerapkan bit-operations dan carrying melalui string yang diterima.

Kekurangan dari metode ini adalah tidak bisa melakukan operasi aritmatika langsung di database, misalnya `SELECT SUM(price) as total_price FROM demo`; tidak akan memungkinkan.

Jika membutuhkan database operations, Postgresql sendiri secara native mendukung NUMERIC yang bisa menyimpan hingga 131rb digit. Jika 4096-bit adalah 1233 digit, NUMERIC di Postgres mendukung 100x lebih banyak digit.
