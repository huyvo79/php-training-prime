FROM php:8.3.9-apache

# Declare composer version
ENV COMPOSER_VERSION=2.7.7

# Install dependencies & PHP extensions
RUN apt-get update \
    && apt-get install -y \
        curl \
        zip \
        unzip \
        git \
        supervisor \
        libpng-dev \
        libpq-dev \
        libzip-dev \
        libexif-dev \
        gnupg \
        tzdata \
        libz-dev \
        libssl-dev \
        pkg-config \
    && docker-php-ext-install \
        bcmath \
        gd \
        opcache \
        sockets \
        zip \
        exif \
        pdo_mysql \
        mysqli \
    && docker-php-ext-enable pdo_mysql \
    # Cài Redis & Xdebug qua PECL
    && pecl install redis xdebug \
    && docker-php-ext-enable redis xdebug

# Cấu hình Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# Install Composer with specified version
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=$COMPOSER_VERSION

# Enable Apache modules
RUN a2enmod rewrite
RUN a2enmod headers

# Add non-root user
RUN useradd -ms /bin/bash -u 1000 app

#COPY start /usr/local/bin/
#CMD [ "start" ]
