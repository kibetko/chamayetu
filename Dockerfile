FROM php:8.4-cli

WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    nodejs \
    npm \
    libpq-dev \
    zip \
    && docker-php-ext-install pdo pdo_pgsql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy project
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node dependencies and build assets
RUN npm install
RUN npm run build

# Cache Laravel configuration
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# Give Laravel permissions
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD sh -c "php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"