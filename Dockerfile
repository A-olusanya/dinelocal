FROM dunglas/frankenphp:php8.4-bookworm

# Ensure session directory exists and is writable
RUN mkdir -p /var/lib/php/sessions && chmod 777 /var/lib/php/sessions

# Configure PHP session path
RUN echo "session.save_path = /var/lib/php/sessions" > /usr/local/etc/php/conf.d/sessions.ini

# Copy Caddyfile to where FrankenPHP expects it
COPY Caddyfile /etc/caddy/Caddyfile

# Copy all app files
COPY . /app

WORKDIR /app
