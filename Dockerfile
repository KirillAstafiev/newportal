FROM php:8.1-fpm

RUN apt-get update && apt-get install -y libldap2-dev && docker-php-ext-install ldap

WORKDIR /var/www/html

COPY api /var/www/html/api

EXPOSE 9000

CMD ["php-fpm"]