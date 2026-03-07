FROM php:8.2-apache

RUN a2enmod rewrite

WORKDIR /var/www/html

COPY public/ /var/www/html/

EXPOSE 80