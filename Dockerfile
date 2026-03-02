FROM php:8.2-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy everything to the root of Apache web server
COPY . /var/www/html/

# Make sure upload folders have the right permissions
# (Note: On Render Free tier, user uploads in these folders will be lost when the server goes to sleep and wakes up again)
RUN chmod -R 777 /var/www/html/covers /var/www/html/pdfs || true

# Expose port 80 (Render detects this and routes traffic to it)
EXPOSE 80
