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
    echo "opcache.max_accelerated_files=4000" >> /usr/local/etc/php/conf.d/z-tcc.ini && \
    echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/z-tcc.ini && \
    echo "error_log = /var/www/html/php-error.log" >> /usr/local/etc/php/conf.d/z-tcc.ini

#Configuração php-fpm
#zz no nome do arquivo para ser o último a carregar
#Gerenciador de processos dinâmico, baseado em max_children, start_servers, min_spare_servers e max_spare_servers
#limite máximo de processos está em 10, pode ser ajustado dado a demanda
#ao iniciar o container, 2 processos estarão ativos
#o mínimo de processos ociosos será de 1, caso não tenha um novo será criado
# o máximo ocioso é de 2, se, por exemplo, sair de um pico de uso de 10 processos apenas 2 serão mantidos
# ao servirem 500 requisições o processo é reiniciado para garantir o funcionamento do script e evitar vazamentos de memória
#esse valor pode ser ajustado ↑ ou ↓ dada a confiabilidade (ou falta de) da aplicação, das bibliotecas e extensões do php
#olog foi ativado, caso ocorra uma lentidão de mais de 5s
RUN echo "[www]" > /usr/local/etc/php-fpm.d/zz-tcc-fpm.conf && \
    echo "pm = dynamic" >> /usr/local/etc/php-fpm.d/zz-tcc-fpm.conf && \
    echo "pm.max_children = 10" >> /usr/local/etc/php-fpm.d/zz-tcc-fpm.conf && \
    echo "pm.start_servers = 2" >> /usr/local/etc/php-fpm.d/zz-tcc-fpm.conf && \
    echo "pm.min_spare_servers = 1" >> /usr/local/etc/php-fpm.d/zz-tcc-fpm.conf && \
    echo "pm.max_spare_servers = 2" >> /usr/local/etc/php-fpm.d/zz-tcc-fpm.conf && \
    echo "pm.max_requests = 500" >> /usr/local/etc/php-fpm.d/zz-tcc-fpm.conf && \
    echo "catch_workers_output = yes" >> /usr/local/etc/php-fpm.d/zz-tcc-fpm.conf && \
    echo "request_slowlog_timeout = 5s" >> /usr/local/etc/php-fpm.d/zz-tcc-fpm.conf && \
    echo "slowlog = /var/www/html/php-fpm-slow.log" >> /usr/local/etc/php-fpm.d/zz-tcc-fpm.conf

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