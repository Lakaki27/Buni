FROM php:8.2-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    wget \
    && rm -rf /var/lib/apt/lists/*

# Enable mod_rewrite
RUN a2enmod rewrite

# Copy .htaccess file
COPY ./.htaccess /var/www/html/.htaccess

# Expose ports
EXPOSE 80 443

# Command to run Apache
CMD ["apache2-foreground"]
