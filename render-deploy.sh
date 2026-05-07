#!/bin/sh

echo "=========================================="
echo "🚀 RENDER DEPLOYMENT START"
echo "=========================================="

# --- Environment ---
export LOG_CHANNEL=stderr
export APP_ENV=production
export APP_DEBUG=false
export APP_URL=https://laravel-ecom-7bos.onrender.com
# Use file drivers to avoid wasting DB connections on sessions/cache
export SESSION_DRIVER=file
export CACHE_STORE=file

# Fix permissions
chmod -R 775 storage bootstrap/cache

# Storage link
echo "[$(date)] 🔗 Linking storage..."
php artisan storage:link || true

# --- Run Migrations (blocking, must finish before server starts) ---
echo "[$(date)] 🐘 Running Migrations..."
php artisan migrate --force --no-interaction
echo "[$(date)] ✅ Migrations done."

# --- Cache everything for production speed ---
# This must run AFTER migrations and BEFORE serving
echo "[$(date)] ⚡ Caching config, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "[$(date)] ✅ Caching done."

# --- Start Server FIRST so Render detects the port immediately ---
echo "[$(date)] 🌐 Starting server on 0.0.0.0:${PORT:-8080}..."
# PHP_CLI_SERVER_WORKERS=2: allows health check + one user request simultaneously
# without exceeding Filess.io's 5-connection limit
export PHP_CLI_SERVER_WORKERS=2
php artisan serve --host=0.0.0.0 --port="${PORT:-8080}" &
SERVER_PID=$!

echo "[$(date)] ✅ Server started (PID $SERVER_PID). Running post-boot setup in background..."

# --- Seeding runs in the background so the server is not blocked ---
(
  sleep 5 # Wait for server to warm up

  echo "[$(date)] 🌱 Checking if seeding is needed..."
  # Temporarily clear config cache to run PHP directly
  CAT_COUNT=$(php -r "
    include 'vendor/autoload.php';
    \$app = include 'bootstrap/app.php';
    \$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    try { echo \App\Models\Category::count(); } catch (Exception \$e) { echo '0'; }
  " 2>/dev/null || echo "0")

  echo "[$(date)] Category count: $CAT_COUNT"

  if [ "$CAT_COUNT" = "0" ]; then
    echo "[$(date)] 🆕 Seeding demo data..."
    php -d memory_limit=-1 artisan db:seed --force --no-interaction
    echo "[$(date)] 🛡️ Generating Shield permissions..."
    php -d memory_limit=-1 artisan shield:generate --all --panel=admin --no-interaction || true
    echo "[$(date)] 👑 Creating Super Admin..."
    php -d memory_limit=-1 artisan shield:super-admin --user=1 --panel=admin --no-interaction || true
    echo "[$(date)] 🖼️ Linking placeholder images..."
    php -d memory_limit=-1 artisan app:download-placeholders || true
    echo "[$(date)] ✅ Seeding complete!"
  else
    echo "[$(date)] ✅ Data exists ($CAT_COUNT categories). Skipping seeding."
  fi
) &

# Wait for the server process to exit (keeps container alive)
wait $SERVER_PID
