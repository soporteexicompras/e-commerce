FROM nginx:1.27-alpine

# Nginx configs
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Aimeos theme static assets (CSS/JS/imgs) - servidos directamente por nginx
# Esto evita hacer roundtrip al PHP-FPM por cada .css/.js
COPY public/vendor /var/www/html/public/vendor
COPY public/css /var/www/html/public/css
COPY public/js /var/www/html/public/js
COPY public/images /var/www/html/public/images
COPY public/aimeos /var/www/html/public/aimeos
COPY public/favicon.ico /var/www/html/public/favicon.ico
