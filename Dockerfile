FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libcurl4-openssl-dev \
    && docker-php-ext-install pdo pdo_mysql curl mbstring \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

RUN sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && printf \"\n<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted\n</Directory>\n\" >> /etc/apache2/apache2.conf

WORKDIR /var/www/html
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html/public/uploads
EXPOSE 80
