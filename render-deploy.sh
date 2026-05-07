#!/bin/sh

# Fail on error
set -e

echo "--------------------------------------------------------"
echo "🚀 RENDER DEPLOYMENT START"
echo "--------------------------------------------------------"

# Force logging to stderr and enable debug mode
export LOG_CHANNEL=stderr
export APP_DEBUG=true
export APP_ENV=production

# Save DB connections by using file-based sessions and cache
export SESSION_DRIVER=file
export CACHE_STORE=file
export DATABASE_URL= # Clear if any to avoid confusion

# 1. Environment Check
echo "🔍 Checking Environment..."
echo "APP_ENV: $APP_ENV"
echo "DB_HOST: $DB_HOST"
echo "DB_PORT: $DB_PORT"

# 2. Fix Permissions & Storage
echo "📂 Setting up storage..."
php artisan storage:link || true
chmod -R 775 storage bootstrap/cache

# 3. Database Connection Test
echo "🧪 Testing Database Connection..."
if php -r "try { new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), [PDO::ATTR_TIMEOUT => 5]); echo '✅ DB Connected!'; } catch (Exception \$e) { echo '❌ DB Connection Failed: ' . \$e->getMessage(); exit(1); }"; then
    echo " Connection success!"
else
    echo " Connection failed!"
    exit 1
fi

# 4. Migrations
echo "🐘 Running Migrations..."
php artisan migrate --force

# 5. Seeding Logic (Simple)
echo "🌱 Checking if seeding is needed..."
# We check for Category count. If it fails, we catch it.
CAT_COUNT=$(php -r "include 'vendor/autoload.php'; \$app = include 'bootstrap/app.php'; \$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap(); try { echo \App\Models\Category::count(); } catch (Exception \$e) { echo '0'; }")

if [ "$CAT_COUNT" = "0" ]; then
    echo "🆕 Database empty. Seeding demo data..."
    php -d memory_limit=-1 artisan db:seed --force
    php -d memory_limit=-1 artisan shield:generate --all --panel=admin --no-interaction || true
    php -d memory_limit=-1 artisan shield:super-admin --user=1 --panel=admin --no-interaction || true
    php -d memory_limit=-1 artisan app:download-placeholders
else
    echo "✅ Data already exists ($CAT_COUNT categories). Skipping seeding."
fi

# 6. Optimization
echo "⚡ Optimizing..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 7. Start Server
echo "🌐 Starting Server on 0.0.0.0:${PORT}..."
# IMPORTANT: Filess.io free tier only allows 5 connections. 
# We MUST set workers to 1 to avoid exceeding this limit.
export PHP_CLI_SERVER_WORKERS=1
exec php -S 0.0.0.0:"${PORT:-8080}" -t public/ public/index.php
