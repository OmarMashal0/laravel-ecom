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
export APP_URL=https://laravel-ecom-7bos.onrender.com

# Save DB connections by using file-based sessions and cache
export SESSION_DRIVER=file
export CACHE_STORE=file

# 1. Environment Check
echo "[$(date)] 🔍 Checking Environment..."
echo "APP_URL: $APP_URL"
echo "DB_HOST: $DB_HOST"

# 2. Fix Permissions & Storage
echo "[$(date)] 📂 Setting up storage..."
php artisan storage:link || true
chmod -R 775 storage bootstrap/cache

# 3. Database Connection Test
echo "[$(date)] 🧪 Testing Database Connection..."
if php -r "try { new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), [PDO::ATTR_TIMEOUT => 10]); echo '✅ DB Connected!'; } catch (Exception \$e) { echo '❌ DB Connection Failed: ' . \$e->getMessage(); exit(1); }"; then
    echo " Connection success!"
else
    echo " Connection failed!"
    exit 1
fi

# 4. Migrations
echo "[$(date)] 🐘 Running Migrations..."
php artisan migrate --force --no-interaction
echo "[$(date)] ✅ Migrations finished."

# 5. Seeding Logic (Simple)
echo "[$(date)] 🌱 Checking if seeding is needed..."
CAT_COUNT=$(php -r "include 'vendor/autoload.php'; \$app = include 'bootstrap/app.php'; \$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap(); try { echo \App\Models\Category::count(); } catch (Exception \$e) { echo '0'; }")

if [ "$CAT_COUNT" = "0" ]; then
    echo "[$(date)] 🆕 Database empty. Seeding demo data (this can take 2-3 mins)..."
    php -d memory_limit=-1 artisan db:seed --force --no-interaction
    echo "[$(date)] 🛡️ Generating Shield permissions..."
    php -d memory_limit=-1 artisan shield:generate --all --panel=admin --no-interaction || true
    echo "[$(date)] 👑 Creating Super Admin..."
    php -d memory_limit=-1 artisan shield:super-admin --user=1 --panel=admin --no-interaction || true
    echo "[$(date)] 🖼️ Linking placeholder images (making HTTP calls)..."
    php -d memory_limit=-1 artisan app:download-placeholders || true
    echo "[$(date)] ✅ Initial seeding finished."
else
    echo "[$(date)] ✅ Data already exists ($CAT_COUNT categories). Skipping seeding."
fi

# 6. Optimization
echo "[$(date)] ⚡ Optimizing..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 7. Start Server
echo "[$(date)] 🌐 Starting Server on 0.0.0.0:${PORT}..."
export PHP_CLI_SERVER_WORKERS=1
exec php -S 0.0.0.0:"${PORT:-8080}" -t public/ public/index.php
