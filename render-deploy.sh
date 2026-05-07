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
# We wrap migration in a loop to wait for DB if needed
MAX_TRIES=5
COUNT=0
while [ $COUNT -lt $MAX_TRIES ]; do
    if php artisan migrate --force; then
        break
    fi
    echo "⚠️ Migration failed, retrying in 5s... ($((COUNT+1))/$MAX_TRIES)"
    sleep 5
    COUNT=$((COUNT+1))
done

# Check if database needs seeding (checks if any categories exist)
# We use a simpler artisan command approach
CAN_SEED=$(php artisan tinker --execute="echo \App\Models\Category::exists() ? 'no' : 'yes';" | tail -n 1 | grep -o "yes\|no" || echo "no")

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
