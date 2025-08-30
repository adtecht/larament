# Use the official PHP 8.3 FPM image as the base runtime
FROM php:8.3-fpm

# --- System packages & PHP extensions ---
# - Update apt metadata
# - Install common build/runtime tools and libraries needed by PHP extensions
# - Build and enable required PHP extensions for Laravel
RUN apt-get update && apt-get install -y \
    git curl zip unzip gnupg ca-certificates \
    libzip-dev libonig-dev libpng-dev libxml2-dev libicu-dev \
    libjpeg-dev libfreetype6-dev libwebp-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
    intl \
    pdo \
    pdo_mysql \
    zip \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd

# --- Node.js (v20 LTS) ---
# Add NodeSource repo for Node 20 and install node + latest npm
# This is useful for running Vite / building frontend assets inside the container
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm

# --- Composer ---
# Copy the Composer binary from the official Composer image into this image
# (avoids installing PHP extensions just to run Composer)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# --- App working directory ---
# Set the working directory for all subsequent commands (COPY, RUN, CMD, etc.)
WORKDIR /var/www

# --- Application source code ---
# Copy the entire application source into the image
# (In dev, a bind mount will typically override this with the host files)
COPY . .

# --- File permissions ---
# Ensure the web user (www-data) owns the codebase
# Grant write/execute permissions to storage/ and bootstrap/cache so Laravel can write logs/cache
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage /var/www/bootstrap/cache

# --- Networking ---
# Document that the app listens on port 8000 inside the container
# (EXPOSE does not publish the port; docker-compose `ports:` does that)
EXPOSE 8000

# --- Startup command ---
# If dependencies are present (vendor/autoload.php), start the Laravel dev server
# binding to all interfaces on port 8000 so it’s reachable from outside the container.
# If not present, keep the container alive (tail -f /dev/null) so you can `docker exec`
# inside and run `composer install`, migrations, etc., without the container exiting.
CMD ["/bin/bash","-c","if [ -f vendor/autoload.php ]; then php artisan serve --host=0.0.0.0 --port=8000; else tail -f /dev/null; fi"]
