FROM php:8.2-apache

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql pgsql pdo_pgsql

# PHP upload and memory settings
RUN echo "file_uploads = On\n\
upload_max_filesize = 50M\n\
post_max_size = 50M\n\
memory_limit = 256M\n\
max_execution_time = 300\n\
max_input_time = 300" > /usr/local/etc/php/conf.d/uploads.ini

COPY . /var/www/html/

EXPOSE 80