# Default Dockerfile
#
# @link     https://www.hyperf.io
# @document https://hyperf.wiki
# @contact  group@hyperf.io
# @license  https://github.com/hyperf/hyperf/blob/master/LICENSE

FROM hyperf/hyperf:8.2-alpine-v3.18-swoole-v5.0
LABEL maintainer="Hyperf Developers <group@hyperf.io>" version="1.0" license="MIT" app.name="Hyperf"

# Set the timezone argument
ARG timezone=America/Sao_Paulo

# Set environment variables
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    TIMEZONE=$timezone \
    APP_ENV=dev \
    SCAN_CACHEABLE=(true)

# update
RUN set -ex \
    # show php version and extensions
    && php -v \
    && php -m \
    && php --ri swoole \
    #  ---------- some config ----------
    && cd /etc/php* \
    # - config PHP
    && { \
        echo "upload_max_filesize=128M"; \
        echo "post_max_size=128M"; \
        echo "memory_limit=1G"; \
        echo "date.timezone=${TIMEZONE}"; \
    } | tee conf.d/99_overrides.ini \
    # - config timezone
    && ln -sf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
    && echo "${TIMEZONE}" > /etc/timezone \
    # ---------- clear works ----------
    && rm -rf /var/cache/apk/* /tmp/* /usr/share/man \
    && echo -e "\033[42;37m Build Completed :).\033[0m\n"

RUN mkdir -p /var/www/app
WORKDIR /var/www/app

COPY . /var/www/app
RUN composer install --no-interaction

EXPOSE 9501

# Command to run the application
WORKDIR /var/www/app
CMD ["php", "/var/www/app/bin/hyperf.php", "server:watch"]
