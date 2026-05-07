#!/bin/sh

echo "=========================================="
echo "🚀 RENDER DEPLOYMENT START (Nginx + PHP-FPM)"
echo "=========================================="

# --- Environment ---
export LOG_CHANNEL=stderr
export APP_ENV=production
export APP_DEBUG=false
export APP_URL=https://laravel-ecom-7bos.onrender.com
export SESSION_DRIVER=file
export CACHE_STORE=file
# Render free tier blocks outbound SMTP (port 587/465).
# Use 'log' driver so order emails are logged but don't crash the app.
export MAIL_MAILER=log

# Fix permissions for PHP-FPM (runs as www-data)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Storage link
echo "[$(date)] 🔗 Linking storage..."
php artisan storage:link || true

# --- Run Migrations (blocking) ---
echo "[$(date)] 🐘 Running Migrations..."
php artisan migrate --force --no-interaction
echo "[$(date)] ✅ Migrations done."

# --- Cache everything for production speed ---
echo "[$(date)] ⚡ Caching config, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "[$(date)] ✅ All caches warm."

# --- Configure nginx with the dynamic PORT from Render ---
export PORT="${PORT:-8080}"
echo "[$(date)] 🔧 Configuring Nginx on port $PORT..."
sed -i "s/PORT_PLACEHOLDER/$PORT/g" /etc/nginx/sites-available/default
# Remove the default nginx site if it exists
rm -f /etc/nginx/sites-enabled/default
ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default
# Test nginx config
nginx -t

# --- Start PHP-FPM in background ---
echo "[$(date)] 🐘 Starting PHP-FPM..."
php-fpm -D
echo "[$(date)] ✅ PHP-FPM started."

# --- Start Nginx in foreground (keeps container alive) ---
echo "[$(date)] 🌐 Starting Nginx..."

# --- Background seeding (runs after server is up) ---
(
  sleep 10

  echo "[$(date)] 🌱 Checking if seeding is needed..."
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
    php -d memory_limit=-1 artisan shield:generate --all --panel=admin --no-interaction || true
    php -d memory_limit=-1 artisan shield:super-admin --user=1 --panel=admin --no-interaction || true
    php -d memory_limit=-1 artisan app:download-placeholders || true
    # Re-warm caches after seeding
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    echo "[$(date)] ✅ Seeding complete and caches refreshed."
  else
    echo "[$(date)] ✅ Data exists ($CAT_COUNT categories). Skipping seeding."
  fi
) &

# Start nginx in the foreground - this keeps the container running
exec nginx -g "daemon off;"
