# PHP 8.3.0 imajını kullanalım
FROM php:8.3.0-fpm

# ICU ve gerekli araçları yükleyin
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# PHP eklentilerini yükleyin
RUN docker-php-ext-install intl \
    && docker-php-ext-configure zip --with-zip \
    && docker-php-ext-install zip pdo pdo_mysql

# Composer'ı yükleyin
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Çalışma dizinini belirleyin
WORKDIR /var/www

# Projenizi çalışma dizinine kopyalayın
COPY . .

# Gerekirse proje bağımlılıklarını yükleyin
RUN composer install

# Uygulamayı ayağa kaldırın
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
