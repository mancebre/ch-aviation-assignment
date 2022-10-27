FROM php:8.1-alpine

RUN apk --update add git openssh bash $PHPIZE_DEPS && \
    rm -rf /var/lib/apt/lists/* && \
    rm /var/cache/apk/*

RUN pecl install xdebug-3.1.5

RUN echo "memory_limit=-1" > "$PHP_INI_DIR/conf.d/memory-limit.ini" \
 && echo "date.timezone=${PHP_TIMEZONE:-UTC}" > "$PHP_INI_DIR/conf.d/date_timezone.ini"

COPY --from=composer:2.4 /usr/bin/composer /usr/bin/composer

WORKDIR /app

