FROM dunglas/frankenphp:php8.4-bookworm

# Copy Caddyfile to where FrankenPHP expects it
COPY Caddyfile /etc/caddy/Caddyfile

# Copy all app files
COPY . /app

WORKDIR /app
