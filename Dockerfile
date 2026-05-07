FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    nodejs \
    npm \
    nginx \
    gettext-base \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd intl \
    && docker-php-ext-enable opcache \
    && rm -rf /var/lib/apt/lists/*

# OPcache configuration for production performance
COPY opcache.ini /usr/local/etc/php/conf.d/opcache.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN npm install && npm run build

RUN chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

COPY nginx.conf /etc/nginx/sites-available/default
COPY render-deploy.sh .
RUN sed -i 's/\r$//' render-deploy.sh
RUN chmod +x render-deploy.sh

CMD ["./render-deploy.sh"]
