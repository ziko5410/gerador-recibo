FROM php:7.4-cli

RUN apt-get update && apt-get -y --no-install-recommends install git \
    && php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install mysqli

COPY ./ /app

WORKDIR /app

RUN composer install

EXPOSE 3333
CMD composer run start
