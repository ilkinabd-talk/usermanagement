FROM php:7.4-apache
LABEL maintainer="ilkin5@mail.ru"


COPY ./ /var/www/html/

# Download script to install PHP extensions and dependencies
ADD https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions /usr/local/bin/

RUN chmod uga+x /usr/local/bin/install-php-extensions && sync

RUN apt-get update &&  \
    apt-get install -y --no-install-recommends \
    libonig-dev\
    gcc \
    libpcre3-dev \
    libxml2-dev \
	zlib1g-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libfreetype6-dev \
    libzip-dev \
    make automake autoconf \
    git && \
    docker-php-ext-install iconv mbstring && \
    docker-php-ext-install pdo pdo_mysql && \
    docker-php-ext-install mysqli && \
    docker-php-ext-install zip xml && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd

#PHP-PSR
RUN git clone https://github.com/jbboehr/php-psr.git /root/psr && \
        cd /root/psr && \
        phpize && \
        ./configure && \
        make && \
        make test && \
        make install && \
        echo "extension=psr.so" > /usr/local/etc/php/conf.d/29-psr.ini && \
        cd && rm -Rf /root/psr
#custom
RUN echo "post_max_size=21M" > /usr/local/etc/php/conf.d/custom.ini &&  \
    echo "upload_max_filesize=20M" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "session.gc_maxlifetime=86400" >> /usr/local/etc/php/conf.d/custom.ini

#get phalcon source
RUN git clone -b v4.1.2 --single-branch git://github.com/phalcon/cphalcon.git /root/buildphalcon

#compile phalcon
RUN cd /root/buildphalcon/build/php7/64bits && \
    phpize && export CFLAGS="-O2 -s" && \
    ./configure && make -j 8 && make install && \
    echo "extension=phalcon.so" > /usr/local/etc/php/conf.d/30-phalcon.ini && \
    rm -Rf /root/buildphalcon

#phalcon dev tool
#RUN cd /root && \
#    git clone https://github.com/phalcon/phalcon-devtools.git && \
#    cd /root/phalcon-devtools && \
#    echo 'alias phalcon="~/phalcon-devtools/phalcon.php"'>> ~/.bashrc
    # ln -s ~/phalcon-devtools/phalcon.php /usr/bin/phalcon && \
    # chmod ugo+x /usr/bin/phalcon

RUN a2enmod rewrite

