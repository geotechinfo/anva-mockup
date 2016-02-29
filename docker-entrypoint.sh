#!/bin/bash
set -e

chown -R www-data:www-data /var/www/html/webservice/
apache2-foreground
