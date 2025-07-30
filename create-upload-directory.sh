#!/bin/bash
# Create user-uploads directory for product images

echo "Creating user-uploads directory..."

# Create the directory structure
mkdir -p /var/www/html/assets/images/user-uploads

# Set proper permissions
chmod 777 /var/www/html/assets/images/user-uploads
chown -R www-data:www-data /var/www/html/assets/images/user-uploads

echo "User-uploads directory created successfully!"
echo "Directory: /var/www/html/assets/images/user-uploads"
echo "Permissions: 777 (www-data:www-data)"
