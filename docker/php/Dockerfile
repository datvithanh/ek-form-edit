FROM php:7.1.17-fpm

COPY ./php.ini /usr/local/etc/php/php.ini

RUN apt-get update && apt-get install -y libmcrypt-dev \
    libmagickwand-dev --no-install-recommends \
    && pecl install imagick\
    && docker-php-ext-enable imagick \
    && docker-php-ext-install mcrypt 
    # && docker-php-ext-install mbstring \
    # && docker-php-ext-enable mbstring

    
RUN docker-php-ext-install pdo pdo_mysql mysqli

# RUN docker-php-ext-install pdo pdo_pgsql pgsql

RUN apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libpng-dev \
    && docker-php-ext-install -j$(nproc) iconv mcrypt \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd

# RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# RUN apt-get install -y libpq-dev \

# RUN docker-php-ext-install pdo pdo_pgsql

# FROM quanca/laravel

COPY ./sync.sh /home/sync.sh

RUN chmod +x /home/sync.sh

# COPY ./id_rsa /root/.ssh/id_rsa
# COPY ./id_rsa.pub /root/.ssh/id_rsa.pub
# COPY ./known_hosts /root/.ssh/known_hosts
# RUN chmod 400 /root/.ssh/id_rsa
# COPY ./composer.phar /usr/local/bin/composer/composer.phar

# RUN apt-get update -y
# RUN apt-get install git -y
# RUN apt-get install nano -y
# RUN git config --global user.name "Dat"
# RUN git config --global user.username "datvithanh"
# RUN git config --global user.email "datvithanh98@gmail.com"

# USER root

# RUN composer global install