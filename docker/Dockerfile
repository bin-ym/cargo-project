FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql gd zip

# Enable Apache modules
RUN a2enmod rewrite

# Allow .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Render PORT support
RUN sed -i 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf && \
    sed -i 's/:80/:${PORT}/' /etc/apache2/sites-available/000-default.conf

# Set working directory
WORKDIR /var/www/html

# Copy project
COPY . /var/www/html/

# Permissions
RUN chown -R www-data:www-data \
    /var/www/html \
    /var/www/html/uploads

EXPOSE 80

CMD ["apache2-foreground"]