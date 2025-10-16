FROM php:8.2-apache

# Set quyền cho storage và bootstrap/cache (Laravel yêu cầu)
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Cài extension cần cho Laravel
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    zip \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Bật rewrite module (Laravel cần cho route)
RUN a2enmod rewrite

# Đặt DocumentRoot thành thư mục public của Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/default-ssl.conf

# Cài Composer
COPY --from=composer:2.8.10 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html/bookingmovieticketapp

# Copy toàn bộ code Laravel
COPY . .

# Set quyền cho storage và bootstrap/cache (Laravel yêu cầu)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8000

# Khi container khởi động thì tự chạy php artisan serve
CMD bash -c "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"




# # If project does not exist, open the comment code below and comment above command, access the bash container to create a project using composer change workdir above with the project name
# FROM php:8.2-apache

# # Cài extension Laravel cần
# RUN apt-get update && apt-get install -y \
#     git unzip curl \
#     libpng-dev libjpeg-dev libfreetype6-dev \
#     libzip-dev libonig-dev zip \
#  && docker-php-ext-configure gd --with-freetype --with-jpeg \
#  && docker-php-ext-install pdo pdo_mysql mbstring bcmath gd zip \
#  && apt-get clean && rm -rf /var/lib/apt/lists/*

# # Bật mod_rewrite cho Apache (Laravel cần)
# RUN a2enmod rewrite

# # Cài Composer (copy từ image composer chính thức)
# COPY --from=composer:2.8.10 /usr/bin/composer /usr/bin/composer

# # Thư mục làm việc
# WORKDIR /var/www/html

# # Mặc định chạy Apache
# CMD ["apache2-foreground"]
