FROM dunglas/frankenphp:php8.4-bookworm

RUN docker-php-ext-install mysqli

COPY . /app

WORKDIR /app