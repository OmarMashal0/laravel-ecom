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
# Returns 0 if categories exist, 1 if not. We want to seed if it returns 1.
CAN_SEED=$(php -r "include 'vendor/autoload.php'; \$app = include 'bootstrap/app.php'; \$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap(); echo \App\Models\Category::exists() ? 'no' : 'yes';")

if [ "$CAN_SEED" = "yes" ]; then
    echo "🌱 Database is empty. Running initial setup..."
    
    echo "📦 Seeding demo data..."
    php artisan db:seed --force
    
    echo "🛡️ Generating Filament Shield permissions..."
    php artisan shield:generate --all --panel=admin --no-interaction
    
    echo "👑 Creating Super Admin (User ID: 1)..."
    php artisan shield:super-admin --user=1 --panel=admin --no-interaction
    
    echo "🖼️ Linking placeholder images..."
    php artisan app:download-placeholders
    
    echo "✅ Initial setup completed successfully."
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
