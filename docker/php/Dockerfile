FROM php:8.4-fpm-alpine AS base

ARG APP_UID=1000
ARG APP_GID=1000

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN apk add --no-cache unzip su-exec \
    && install-php-extensions pdo_pgsql opcache \
    && addgroup -g ${APP_GID} app \
    && adduser -D -u ${APP_UID} -G app app \
    && mkdir -p /home/app/.composer/cache \
    && chown -R app:app /home/app \
    && printf '[www]\nuser = app\ngroup = app\n' > /usr/local/etc/php-fpm.d/zz-app.conf

ENV COMPOSER_HOME=/home/app/.composer

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint

RUN chmod +x /usr/local/bin/entrypoint

WORKDIR /var/www/html

ENTRYPOINT ["entrypoint"]

EXPOSE 9000
CMD ["php-fpm"]


FROM base AS development

ARG XDEBUG_MODE=develop,coverage,debug,profile
ARG XDEBUG_HOST=host.docker.internal
ARG XDEBUG_IDE_KEY=DOCKER
ARG XDEBUG_START=yes
ARG XDEBUG_LOG_LEVEL=0

RUN install-php-extensions xdebug \
    && { \
        echo "xdebug.mode=${XDEBUG_MODE}"; \
        echo "xdebug.client_host=${XDEBUG_HOST}"; \
        echo "xdebug.idekey=${XDEBUG_IDE_KEY}"; \
        echo "xdebug.start_with_request=${XDEBUG_START}"; \
        echo "xdebug.log_level=${XDEBUG_LOG_LEVEL}"; \
        echo "xdebug.output_dir=/tmp"; \
    } > /usr/local/etc/php/conf.d/zz-xdebug.ini
