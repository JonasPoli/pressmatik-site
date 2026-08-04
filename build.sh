#!/bin/bash
set -e

# Cores para o output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

# Detectar binário do PHP
if [ -z "$PHP_BIN" ]; then
    if [ -f "/RunCloud/Packages/php84rc/bin/php" ]; then
        PHP_BIN="/RunCloud/Packages/php84rc/bin/php"
    elif [ -f "/opt/cpanel/ea-php84/root/bin/php" ]; then
        PHP_BIN="/opt/cpanel/ea-php84/root/bin/php"
    elif [ -f "/opt/cpanel/ea-php83/root/bin/php" ]; then
        PHP_BIN="/opt/cpanel/ea-php83/root/bin/php"
    elif command -v php84 &> /dev/null; then
        PHP_BIN="php84"
    elif command -v php83 &> /dev/null; then
        PHP_BIN="php83"
    else
        PHP_BIN="php"
    fi
fi

echo -e "${BLUE}==> Pressmatik Deploy Script${NC}"
echo -e "${GREEN}--> PHP: $PHP_BIN${NC}"

# ─────────────────────────────────────────────────────────────────────
# CRÍTICO: Forçar APP_ENV=prod ANTES de qualquer comando Symfony
# Sem isso: x-robots-tag: noindex, cache desabilitado, debug ativo
# ─────────────────────────────────────────────────────────────────────
echo -e "${RED}==> CONFIGURANDO AMBIENTE DE PRODUÇÃO${NC}"
export APP_ENV=prod
export APP_DEBUG=0

# Reescrever .env.local garantindo prod (remove APP_ENV=dev anterior)
if [ -f ".env.local" ]; then
    grep -v "^APP_ENV=" .env.local | grep -v "^APP_DEBUG=" > .env.local.tmp 2>/dev/null || true
    mv .env.local.tmp .env.local
fi

printf "APP_ENV=prod\nAPP_DEBUG=0\n" >> .env.local
echo -e "${GREEN}--> APP_ENV=prod configurado em .env.local${NC}"

# ─────────────────────────────────────────────────────────────────────
# 0. Instalar dependências (--no-dev para prod)
# ─────────────────────────────────────────────────────────────────────
if [ -f "composer.json" ]; then
    echo -e "${GREEN}--> Composer install (prod, sem dev)...${NC}"
    COMPOSER_PATH=$(which composer 2>/dev/null || echo "/usr/local/bin/composer")
    if [ -f "$COMPOSER_PATH" ]; then
        APP_ENV=prod $PHP_BIN "$COMPOSER_PATH" install --no-interaction --optimize-autoloader --no-dev
    else
        APP_ENV=prod $PHP_BIN composer install --no-interaction --optimize-autoloader --no-dev
    fi
fi

# ─────────────────────────────────────────────────────────────────────
# 1. Limpar e aquecer cache (prod)
# ─────────────────────────────────────────────────────────────────────
echo -e "${GREEN}--> Limpando cache (prod)...${NC}"
rm -rf var/cache/*
APP_ENV=prod $PHP_BIN bin/console cache:clear --env=prod
APP_ENV=prod $PHP_BIN bin/console cache:warmup --env=prod

# ─────────────────────────────────────────────────────────────────────
# 2. Otimizar imagens → WebP
# ─────────────────────────────────────────────────────────────────────
echo -e "${GREEN}--> Otimizando imagens (WebP)...${NC}"
APP_ENV=prod $PHP_BIN bin/console app:optimize-images --env=prod

# ─────────────────────────────────────────────────────────────────────
# 3. Compilar CSS (minificado)
# ─────────────────────────────────────────────────────────────────────
echo -e "${GREEN}--> Minificando app.css...${NC}"
$PHP_BIN -r "
\$file = 'assets/styles/app.css';
if (file_exists(\$file)) {
    \$css = file_get_contents(\$file);
    \$min = preg_replace('/\/\*[\s\S]*?\*\//', '', \$css);
    \$min = preg_replace('/\s+/', ' ', \$min);
    \$min = preg_replace('/\s*([{}:;,])\s*/', '$1', \$min);
    \$min = str_replace(';}', '}', \$min);
    file_put_contents(\$file, trim(\$min));
}
"
APP_ENV=prod $PHP_BIN bin/console tailwind:build --minify

# ─────────────────────────────────────────────────────────────────────
# 4. Compilar Assets com hashes de versão
# ─────────────────────────────────────────────────────────────────────
echo -e "${GREEN}--> Compilando assets...${NC}"
APP_ENV=prod $PHP_BIN bin/console asset-map:compile

# ─────────────────────────────────────────────────────────────────────
# 5. Limpar cache de miniaturas
# ─────────────────────────────────────────────────────────────────────
if [ -d "public/media/cache" ]; then
    echo -e "${GREEN}--> Removendo cache de miniaturas...${NC}"
    rm -rf public/media/cache/*
fi
$PHP_BIN bin/console liip:imagine:cache:remove --env=prod 2>/dev/null || true

# ─────────────────────────────────────────────────────────────────────
# 6. Limpar logs
# ─────────────────────────────────────────────────────────────────────
echo -e "${GREEN}--> Limpando logs...${NC}"
rm -rf var/log/*

echo -e "${BLUE}==> Deploy concluído! APP_ENV=prod ativo.${NC}"
echo -e "${GREEN}--> Verifique: curl -sI https://pressmatik.wab.com.br/pt/ | grep -i robots${NC}"
