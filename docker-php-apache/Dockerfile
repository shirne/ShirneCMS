FROM php:7.2.28-apache

# 修改apt源为阿里云镜像
RUN cp /etc/apt/sources.list /etc/apt/sources.list.bak
RUN echo 'deb https://mirrors.aliyun.com/debian stable main contrib non-free' > /etc/apt/sources.list \
    && echo 'deb https://mirrors.aliyun.com/debian stable-updates main contrib non-free' >> /etc/apt/sources.list 

# php modules
RUN apt-get update \
    && apt-get install -y \
    libxmp-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    devscripts
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-webp-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/ \
    && docker-php-ext-install exif gd bcmath mysqli pdo_mysql zip

RUN mkdir -p /data/wwwroot/shirnecms/public
RUN echo '<?php phpinfo();' > /data/wwwroot/shirnecms/public/index.php

# 可以拷贝文件到镜像
#COPY ../src /data/wwwroot/shirnecms

#COPY ../dbscript /data/wwwroot/shirnecms/dbscript

COPY php.ini-dev /usr/local/etc/php
COPY sites.conf /etc/apache2/sites-available/000-default.conf
RUN chown www-data:www-data -Rf /data/wwwroot

# 修改默认端口
# EXPOSE 80

WORKDIR /data/wwwroot/shirnecms