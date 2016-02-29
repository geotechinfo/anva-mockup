# Set the base image to Apache-PHP
FROM php:5.5-apache

# Project author and maintainer
MAINTAINER Santanu Brahma <santanu.brahma@geotechinfo.net>

# Update and upgrade sources
RUN apt-get update
RUN apt-get -y upgrade
RUN apt-get -y -q install vim

# Install mod_rewrite
RUN a2enmod rewrite

# Configure apache with config
COPY apache2.conf /etc/apache2/apache2.conf
EXPOSE 80

# Install postgreSQL package
#RUN apt-get -y -q install python-software-properties software-properties-common
#RUN apt-get -y -q install postgresql-9.4 postgresql-client-9.4 postgresql-contrib-9.4

# Copy project source to instance
RUN mkdir /var/www/html/webservice/
COPY phpinfo.php /var/www/html/webservice/
COPY webservice/ /var/www/html/webservice/
COPY .env /var/www/html/webservice/

# Set application permission
RUN chmod 777 -R /var/www/html/webservice/storage
RUN chmod 777 -R /var/www/html/webservice/vendor/laravel/lumen-framework/storage/logs
RUN chmod 777 -R /var/www/html/webservice/vendor/psr/log

# Run apache at startup
#COPY docker-entrypoint.sh /
#RUN chmod u+x /docker-entrypoint.sh
#ENTRYPOINT ["/docker-entrypoint.sh"]