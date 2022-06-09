FROM php:7.4.22-apache

RUN cp /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load

RUN docker-php-ext-install calendar pdo_mysql
RUN pecl install xdebug &&  docker-php-ext-install mysqli && docker-php-ext-enable xdebug mysqli
ADD docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

ENV APACHE_RUN_USER apache
RUN useradd -d /var/www --uid=1000 --group=www-data -s /sbin/nologin  apache
