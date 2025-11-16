FROM dunglas/frankenphp:php8.2

WORKDIR /app

RUN apt-get update && apt-get install -y \
    libjpeg-dev \
    libpng-dev \
    libwebp-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    default-mysql-client \
    zip \
    unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install \
    gd \
    mysqli \
    pdo \
    pdo_mysql \
    zip \
    && pecl install redis \
    && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

COPY docker/php/CaddyfileConfig /etc/caddy/Caddyfile
COPY docker/php/php-custom.ini /usr/local/etc/php/conf.d/99-custom.ini

EXPOSE 80

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile", "--adapter", "caddyfile"]