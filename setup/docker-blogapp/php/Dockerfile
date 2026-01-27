FROM php:8.4-fpm

# Install system dependencies
RUN apt update && apt install -y \
    libzip-dev \
    libonig-dev \
    curl \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring

# Modify www-data user to match host user
ARG LOCAL_UID=1000
ARG LOCAL_GID=1000

RUN groupmod -g ${LOCAL_GID} www-data && \
    usermod -u ${LOCAL_UID} -g ${LOCAL_GID} www-data

COPY ./.bashrc /home/www-data/.bashrc
RUN chown www-data:www-data /home/www-data/.bashrc

# Configure Git and shell
RUN git config --global --add safe.directory '*' && \
    chsh -s /bin/bash www-data

# Set working directory
WORKDIR /var/www/html

# Switch to www-data user
USER www-data

EXPOSE 9000