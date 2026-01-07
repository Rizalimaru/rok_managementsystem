# Gunakan PHP Versi 8.2
FROM php:8.2-cli

# Install library sistem yang dibutuhkan (Termasuk SQLite)
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    unzip \
    git \
    libzip-dev

# Install Extension PHP untuk Laravel & Database
RUN docker-php-ext-install pdo_sqlite bcmath zip

# Install Composer (Manajer Paket PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set folder kerja di dalam server
WORKDIR /var/www

# Copy semua file codingan Anda ke server
COPY . .

# Install library Laravel (Vendor)
RUN composer install --no-dev --optimize-autoloader

# Atur izin folder storage agar bisa menyimpan log/cache
RUN chmod -R 777 storage bootstrap/cache

# Jalankan perintah Migrate Database & Nyalakan Server
# Kita gunakan port 10000 (Port standar Render)
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000