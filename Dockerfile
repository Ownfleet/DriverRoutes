FROM php:8.2-apache

RUN sed -i "s/80/\${PORT}/g" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

COPY public/ /var/www/html/

EXPOSE 80