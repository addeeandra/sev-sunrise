## Harbinger

> The name's doesn't matter.

Prasyarat untuk running project:

- PHP & Composer
- Docker (running Pgsql, MariaDb, Mysql)

### Setup Guide

Paling mudah menggunakan Docker dengan asumsi PHP dan Composer terinstall. Bisa jalankan perintah berikut:

1. Copy `.env.example` ke `.env`
2. Jalankan `docker compose up -d` (ini akan serving 3 database: pgsql, mariadb, dan mysql)
3. Jalankan `php a migrate --database=pgsql|mysql|mariadb --seed` (ini akan migrate table dan seed yang dibutuhkan sesuai database pilihan)
4. Jalankan `php a app:calculate-2048` atau `php a app:calculate-4096`

Bisa akses adminer di http://localhost:8080. Masukkan kredensial sesuai dengan `.env`, tapi untuk database host sesuaikan dengan nama service 
di docker-compose (e.g. pgsql|mariadb|mysql)
