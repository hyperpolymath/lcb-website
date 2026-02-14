# SPDX-License-Identifier: PMPL-1.0-or-later
# Containerfile - Multi-stage WordPress build for lcb-website
# stapeln scheme: Chainguard wolfi-base + WordPress 6.9 + LiteSpeed + Sinople
#
# Build:   podman build -f Containerfile -t lcb-wordpress:6.9.0 .
# Sign:    cerro-torre sign lcb-wordpress:6.9.0
# Seal:    selur seal lcb-wordpress:6.9.0
# Verify:  cerro-torre verify lcb-wordpress:6.9.0

# ==========================================================================
# Stage 1: Build dependencies
# ==========================================================================
FROM cgr.dev/chainguard/wolfi-base:latest AS build

# Install PHP 8.4, Composer, and build tools
RUN apk add --no-cache \
    php-8.4 \
    php-8.4-curl \
    php-8.4-dom \
    php-8.4-gd \
    php-8.4-intl \
    php-8.4-mbstring \
    php-8.4-mysqli \
    php-8.4-openssl \
    php-8.4-phar \
    php-8.4-xml \
    php-8.4-zip \
    php-8.4-redis \
    php-8.4-opcache \
    php-8.4-imagick \
    curl \
    unzip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Download WordPress 6.9
WORKDIR /build
RUN curl -sL https://wordpress.org/wordpress-6.9.tar.gz | tar xz \
    && mv wordpress /var/www/html

# Copy and install Sinople theme dependencies
COPY wp-content/themes/sinople/ /var/www/html/wp-content/themes/sinople/
RUN if [ -f /var/www/html/wp-content/themes/sinople/composer.json ]; then \
      cd /var/www/html/wp-content/themes/sinople && composer install --no-dev --optimize-autoloader; \
    fi

# Copy mu-plugins (php-aegis)
COPY wp-content/mu-plugins/ /var/www/html/wp-content/mu-plugins/

# ==========================================================================
# Stage 2: Runtime
# ==========================================================================
FROM cgr.dev/chainguard/wolfi-base:latest AS runtime

# OCI annotations
LABEL org.opencontainers.image.title="LCB WordPress" \
      org.opencontainers.image.description="Hardened WordPress 6.9 for NUJ London Central Branch with Sinople theme, php-aegis, and consent-aware HTTP" \
      org.opencontainers.image.source="https://github.com/hyperpolymath/lcb-website" \
      org.opencontainers.image.version="6.9.0" \
      org.opencontainers.image.licenses="PMPL-1.0-or-later" \
      org.opencontainers.image.vendor="hyperpolymath"

# Install PHP 8.4 runtime + LiteSpeed
RUN apk add --no-cache \
    php-8.4 \
    php-8.4-curl \
    php-8.4-dom \
    php-8.4-gd \
    php-8.4-intl \
    php-8.4-mbstring \
    php-8.4-mysqli \
    php-8.4-openssl \
    php-8.4-xml \
    php-8.4-zip \
    php-8.4-redis \
    php-8.4-opcache \
    php-8.4-imagick \
    php-8.4-fpm \
    litespeed

# Copy WordPress from build stage
COPY --from=build /var/www/html /var/www/html

# Copy .well-known files
COPY .well-known/ /var/www/html/.well-known/

# Copy robots.txt
COPY robots.txt /var/www/html/robots.txt

# Security: non-root user
RUN adduser -D -u 1000 wordpress \
    && chown -R wordpress:wordpress /var/www/html

# LiteSpeed configuration
COPY services/openlitespeed/ /usr/local/lsws/conf/ 2>/dev/null || true

# Health check
HEALTHCHECK --interval=30s --timeout=5s --retries=3 \
    CMD curl -sf http://localhost:8080/ || exit 1

EXPOSE 8080

USER wordpress

ENTRYPOINT ["/usr/local/lsws/bin/lswsctrl", "start"]
