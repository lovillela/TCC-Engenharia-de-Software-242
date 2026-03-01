FROM httpd:latest

# Enable required Apache modules and add Listen directive for HTTPS
RUN sed -i \
    -e 's/#LoadModule rewrite_module/LoadModule rewrite_module/' \
    -e 's/#LoadModule proxy_module/LoadModule proxy_module/' \
    -e 's/#LoadModule proxy_fcgi_module/LoadModule proxy_fcgi_module/' \
    -e 's/#LoadModule proxy_http_module/LoadModule proxy_http_module/' \
    -e 's/#LoadModule socache_shmcb_module/LoadModule socache_shmcb_module/' \
    -e 's/#LoadModule ssl_module/LoadModule ssl_module/' \
    -e 's/#LoadModule http2_module/LoadModule http2_module/' \
    -e 's/#LoadModule headers_module/LoadModule headers_module/' \
    -e 's/#LoadModule mod_deflate/LoadModule mod_deflate/' \
    -e 's/#LoadModule filter_module/LoadModule filter_module/' \
    /usr/local/apache2/conf/httpd.conf && \
    echo "Listen 443" >> /usr/local/apache2/conf/httpd.conf && \
    echo "ServerName localhost" >> /usr/local/apache2/conf/httpd.conf

# Copy custom configuration and SSL certificates
COPY ./apache.conf /usr/local/apache2/conf/extra/php-fpm.conf
RUN mkdir -p /usr/local/apache2/conf/certs/
COPY cert.pem /usr/local/apache2/conf/certs/apache-selfsigned.crt
COPY key.pem /usr/local/apache2/conf/certs/apache-selfsigned.key

# Include our custom configuration
RUN echo "Include conf/extra/php-fpm.conf" >> /usr/local/apache2/conf/httpd.conf