FROM php:8.3-fpm-alpine

# Install nginx, supervisor, envsubst (gettext), and SQLite
RUN apk add --no-cache \
    nginx \
    supervisor \
    gettext \
    sqlite-dev \
    && docker-php-ext-install pdo_sqlite

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files first for layer caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy application code
COPY . .

# Re-run composer for post-install scripts (if any) with full source
RUN composer dump-autoload --no-dev --optimize

# Pass env vars through PHP-FPM to PHP workers
RUN sed -i 's/;clear_env = no/clear_env = no/' /usr/local/etc/php-fpm.d/www.conf

# Persist sessions on the volume
RUN echo 'session.save_path = "/data/sessions"' > /usr/local/etc/php/conf.d/sessions.ini

# Create required directories
RUN mkdir -p /data/sessions /var/log/supervisor /var/run/nginx \
    && chown -R www-data:www-data /data /var/www/html

# Copy config files
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/nginx/railway.conf /etc/nginx/railway.conf.template
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]
