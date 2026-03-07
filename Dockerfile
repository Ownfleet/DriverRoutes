FROM php:8.2-apache

RUN a2dismod mpm_event || true \
 && a2dismod mpm_worker || true \
 && a2enmod mpm_prefork \
 && a2enmod rewrite

WORKDIR /var/www/html

COPY public/ /var/www/html/

EXPOSE 80