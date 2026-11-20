# Vývojový Apache + PHP. Verzi PHP měníte přes build arg / .env (PHP_VERSION).
ARG PHP_VERSION=8.4
FROM php:${PHP_VERSION}-apache-bookworm

ARG PHP_VERSION
ENV PHP_VERSION=${PHP_VERSION}

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        curl \
        socat \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libzip-dev \
        libicu-dev \
        libonig-dev \
        openssl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        gd \
        intl \
        mbstring \
        mysqli \
        pdo_mysql \
        zip \
        opcache \
    && a2enmod rewrite ssl headers \
    && rm -rf /var/lib/apt/lists/*

# Composer (pro `docker compose exec web composer …`)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY php.ini /usr/local/etc/php/conf.d/99-grafia-web.ini
COPY entrypoint.sh /usr/local/bin/grafia-web-entrypoint.sh
# Windows checkout (autocrlf) → CRLF by rozbil shebang („no such file or directory“).
RUN sed -i 's/\r$//' /usr/local/bin/grafia-web-entrypoint.sh \
    && chmod +x /usr/local/bin/grafia-web-entrypoint.sh

WORKDIR /var/www/html

# DocumentRoot = /var/www/html (bind-mount kořene repo). Vhosty se mountují z docker/apache/.
# Listen 443 pro HTTPS vhost.
RUN printf '\nListen 443\n' >> /etc/apache2/ports.conf

EXPOSE 80 443

ENTRYPOINT ["/usr/local/bin/grafia-web-entrypoint.sh"]
CMD ["apache2-foreground"]
