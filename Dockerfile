FROM dunglas/frankenphp:php8.4-bookworm

RUN install-php-extensions mysqli

COPY . /app/public