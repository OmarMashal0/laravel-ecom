#!/bin/sh

# Fail on error
set -e

echo "--------------------------------------------------------"
echo "🚀 Starting Render Deployment Script"
echo "--------------------------------------------------------"

# Ensure storage link exists
echo "🔗 Linking storage..."
php artisan storage:link || true

# Run migrations
echo "🐘 Running migrations..."
php artisan migrate --force

# Check if database needs seeding (checks if any categories exist)
# We use a more robust check that handles potential PHP warnings
CAN_SEED=$(php -d memory_limit=-1 -r "include 'vendor/autoload.php'; \$app = include 'bootstrap/app.php'; \$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap(); echo \App\Models\Category::exists() ? 'no' : 'yes';" 2>/dev/null | tail -n 1)

if [ "$CAN_SEED" = "yes" ]; then
    echo "🌱 Database is empty. Running initial setup..."
    
    echo "📦 Seeding demo data (this may take a few minutes)..."
    php -d memory_limit=-1 artisan db:seed --force
    
    echo "🛡️ Generating Filament Shield permissions..."
    # Increasing memory limit and adding verbose output on failure
    php -d memory_limit=-1 artisan shield:generate --all --panel=admin --no-interaction || { echo "❌ Shield generation failed. Continuing anyway..."; }
    
    echo "👑 Creating Super Admin (User ID: 1)..."
    php -d memory_limit=-1 artisan shield:super-admin --user=1 --panel=admin --no-interaction || { echo "❌ Super-admin assignment failed. Continuing anyway..."; }
    
    echo "🖼️ Linking placeholder images..."
    php -d memory_limit=-1 artisan app:download-placeholders
    
    echo "✅ Initial setup process finished."
else
    echo "ℹ️ Database already contains data. Skipping initial setup."
fi

# Clear and Cache for production performance
echo "⚡ Optimizing application..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
# Note: config:cache is omitted to ensure Render's dynamic PORT is picked up correctly

echo "🌐 Starting server on port ${PORT}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
