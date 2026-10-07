FROM dunglas/frankenphp:php8.4-bookworm

RUN docker-php-ext-install mysqli

COPY . /app
COPY Caddyfile /etc/caddy/Caddyfile