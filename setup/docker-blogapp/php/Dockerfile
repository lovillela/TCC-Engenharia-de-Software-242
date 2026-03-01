FROM php:8.4-fpm

# Atualização de pacotes e instalação
RUN apt update && apt install -y \
    libzip-dev \
    libonig-dev \
    curl \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

#RUN apt upgrade -y

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalação de extensões
RUN docker-php-ext-install \
    pdo_mysql \
    zip \
    mbstring \
    opcache

# Copiando o ini padrão de produção para o container
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# Uso de arquivo de configuração unificado no contexto do tcc
# Letra 'z' no inicio do nome do arquivo para garantir que seja o ultimo a carregar
# Removido powerd by, erros não irão aparecer em prod
# Ativação do opcache, limite de memoria em 128MB, 8MB para strings repetidas
# Limite de armazenamento de 4 mil arquivos no opcahe
RUN echo "expose_php = Off" >> /usr/local/etc/php/conf.d/z-tcc.ini && \ 
    echo "display_errors = Off" >> /usr/local/etc/php/conf.d/z-tcc.ini && \
    echo "session.cookie_secure = 1" >> /usr/local/etc/php/conf.d/z-tcc.ini && \
    echo "session.cookie_httponly = 1" >> /usr/local/etc/php/conf.d/z-tcc.ini && \
    echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/z-tcc.ini && \
    echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/z-tcc.ini && \
    echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/z-tcc.ini && \
    echo "opcache.max_accelerated_files=4000" >> /usr/local/etc/php/conf.d/z-tcc.ini

#RUN echo 

# Modificando o www-data para ser igual ao host
ARG LOCAL_UID=1000
ARG LOCAL_GID=1000

RUN groupmod -g ${LOCAL_GID} www-data && \
    usermod -u ${LOCAL_UID} -g ${LOCAL_GID} www-data

COPY ./.bashrc /home/www-data/.bashrc
RUN chown www-data:www-data /home/www-data/.bashrc

# Configure Git and shell
RUN git config --global --add safe.directory '*' && \
    chsh -s /bin/bash www-data

# Set working directory
WORKDIR /var/www/html

# Switch to www-data user
USER www-data

EXPOSE 9000