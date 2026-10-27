FROM php:8.5.12-fpm-alpine

RUN apk add --no-cache \
    nginx \
    supervisor \
    git \
    openssh-client \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) \
    gd \
    mbstring \
    zip \
    intl \
    exif

# Production php.ini (display_errors off, etc.) + hardening
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
 && { \
      echo 'expose_php = Off'; \
      echo 'upload_max_filesize = 100M'; \
      echo 'post_max_size = 100M'; \
      echo 'opcache.enable = 1'; \
      echo 'opcache.validate_timestamps = 1'; \
      echo 'opcache.revalidate_freq = 2'; \
    } > "$PHP_INI_DIR/conf.d/zz-well.ini"

# Run php-fpm as nginx (uid 100) to match ownership of the existing prod volume;
# allow root (deploy's `docker exec ... git pull`) to operate on that checkout
RUN sed -i 's/^user = www-data/user = nginx/; s/^group = www-data/group = nginx/' /usr/local/etc/php-fpm.d/www.conf \
 && git config --system --add safe.directory /var/www/html

COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf

WORKDIR /var/www/html

# Bake the app in; models/my_settings.php (secrets) and data dirs are mounted at runtime.
# Locally, docker-compose bind-mounts the repo over this.
COPY . /var/www/html

RUN mkdir -p /run/nginx temp results RadarUserCSV \
 && chown -R nginx:nginx temp results RadarUserCSV

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
