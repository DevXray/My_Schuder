#!/bin/bash
set -e

cd /var/www/html

wait_for_mysql() {
    if [ "$DB_CONNECTION" = "mysql" ] && [ -n "$DB_HOST" ]; then
        echo "[entrypoint] Menunggu MySQL di ${DB_HOST}:${DB_PORT:-3306}..."
        until nc -z -w1 "$DB_HOST" "${DB_PORT:-3306}"; do
            sleep 1
        done
        echo "[entrypoint] MySQL sudah bisa diakses."
    fi
}

wait_for_mysql

# Kalau belum ada .env sama sekali (mis. dev container fresh), pinjam dulu dari example.
# Di prod normalnya .env sudah disuntik lewat env_file, jadi blok ini praktis no-op.
if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

# Generate APP_KEY HANYA kalau belum ada. Meng-generate ulang di setiap restart akan
# membuat semua session & data terenkripsi yang sudah ada jadi tidak terbaca lagi.
if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    php artisan key:generate --ansi --force
fi

if [ "$APP_ENV" = "local" ]; then
    # Dev: auto-sync schema tiap start, aman karena cuma 1 instance dan datanya disposable.
    php artisan migrate --force

    # nginx (dev) bind-mount folder host yang sama dengan app, jadi symlink yang
    # dibuat di sini otomatis kelihatan oleh nginx juga. TANPA baris ini, upload
    # Materi/Tugas akan 404 di browser walau proses upload-nya sendiri sukses.
    php artisan storage:link 2>/dev/null || true
else
    # Production: SENGAJA tidak auto-migrate di sini.
    # Migration harus jadi langkah manual/CI yang terpisah dan disengaja:
    #   docker compose -f docker-compose.prod.yml run --rm app php artisan migrate --force
    # Auto-migrate di entrypoint berbahaya begitu kamu punya >1 replica (race condition)
    # atau migration yang destructive ke-deploy tanpa review.
    #
    # storage:link SENGAJA tidak dipanggil di sini — nginx (prod) tidak share
    # filesystem dengan app, jadi symlink yang dibuat di container app tidak akan
    # kelihatan oleh nginx. Solusinya bukan symlink, tapi named volume app-storage
    # yang di-mount langsung ke public/storage di service nginx (lihat docker-compose.prod.yml).
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

exec "$@"
