#!/bin/bash
set -e

# Instala/atualiza dependências do Composer na inicialização do container
# Necessário pois o código-fonte é montado via volume (não disponível no build)
if [ -f /var/www/html/composer.json ]; then
    echo "[entrypoint] composer.json encontrado, executando composer install..."
    composer update --no-interaction --optimize-autoloader
else
    echo "[entrypoint] composer.json não encontrado em /var/www/html, pulando composer install."
fi

# Executa o comando padrão do container (php-fpm)
exec "$@"