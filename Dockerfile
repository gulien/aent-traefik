FROM thecodingmachine/php:7.2-v1-cli as builder

COPY composer.json composer.json
COPY composer.lock composer.lock

RUN composer install --no-dev

FROM theaentmachine/base-php-aent:0.0.15

# Copies our aent entry point.
COPY aent.sh /usr/bin/aent

# Copies vendor directory.
COPY --from=builder /usr/src/app/vendor ./vendor

# Copies our PHP source.
COPY ./src ./src
