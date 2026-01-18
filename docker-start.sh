#!/bin/bash

echo "🐳 Starting WAGW Docker Setup..."
echo ""

# Check if .env exists
if [ ! -f .env ]; then
    echo "📝 Creating .env file from env.docker.example..."
    cp env.docker.example .env
    echo "✅ .env file created. Please edit it with your configuration."
    echo ""
fi

# Check if APP_KEY is set
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo "🔑 Generating application key..."
    docker-compose run --rm app php artisan key:generate
    echo ""
fi

# Build images
echo "🔨 Building Docker images..."
docker-compose build

# Start services
echo "🚀 Starting services..."
docker-compose up -d

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
sleep 10

# Run migrations
echo "📊 Running database migrations..."
docker-compose exec -T app php artisan migrate --force

# Run seeders
echo "🌱 Seeding database..."
docker-compose exec -T app php artisan db:seed --force

# Optimize application
echo "⚡ Optimizing application..."
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan route:cache
docker-compose exec -T app php artisan view:cache

echo ""
echo "✅ Setup complete!"
echo ""
echo "📱 Access your application:"
echo "   - Web App: http://localhost"
echo "   - Node.js Worker: http://localhost:3000"
echo "   - phpMyAdmin: http://localhost:8080"
echo ""
echo "📝 Default admin credentials:"
echo "   - Email: admin@admin.com"
echo "   - Password: password"
echo ""
echo "📚 For more information, see DOCKER.md"
