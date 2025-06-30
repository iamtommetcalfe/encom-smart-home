#!/bin/bash

# Encom Smart Home Web App - Setup Script
echo "=== Encom Smart Home Web App - Setup Script ==="
echo "This script will help you set up the initial Laravel project for Encom."
echo

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "PHP is not installed. Please install PHP 8.2+ before continuing."
    exit 1
fi

# Check PHP version
PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo "PHP Version: $PHP_VERSION"

# Check if Composer is installed
if ! command -v composer &> /dev/null; then
    echo "Composer is not installed. Please install Composer before continuing."
    exit 1
fi

echo "Composer is installed."

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo "Node.js is not installed. Please install Node.js before continuing."
    exit 1
fi

NODE_VERSION=$(node -v)
echo "Node.js Version: $NODE_VERSION"

# Check if npm is installed
if ! command -v npm &> /dev/null; then
    echo "npm is not installed. Please install npm before continuing."
    exit 1
fi

NPM_VERSION=$(npm -v)
echo "npm Version: $NPM_VERSION"

echo
echo "All prerequisites are installed. Ready to set up the Encom project."
echo

# Ask for confirmation before proceeding
read -p "Do you want to create a new Laravel project for Encom? (y/n): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Setup cancelled."
    exit 1
fi

# Create Laravel project
echo "Creating Laravel project..."
# Create a temporary directory for Laravel installation
TEMP_DIR="temp_laravel_install"
mkdir -p $TEMP_DIR

# Install Laravel in the temporary directory
composer create-project laravel/laravel $TEMP_DIR

# Move all Laravel files to the current directory
echo "Moving Laravel files to the current directory..."
# First move all non-hidden files
mv $TEMP_DIR/* . 2>/dev/null || true
# Then move all hidden files (except . and ..)
find $TEMP_DIR -maxdepth 1 -name ".*" -not -name "." -not -name ".." -exec mv {} . \;

# Remove the temporary directory
rm -rf $TEMP_DIR

# Install TypeScript
echo "Installing TypeScript..."
npm install --save-dev typescript

# Create tsconfig.json
echo "Creating TypeScript configuration..."
cat > tsconfig.json << 'EOL'
{
    "compilerOptions": {
        "target": "es2015",
        "module": "esnext",
        "moduleResolution": "node",
        "strict": true,
        "jsx": "preserve",
        "sourceMap": true,
        "resolveJsonModule": true,
        "esModuleInterop": true,
        "lib": ["esnext", "dom"],
        "types": ["vite/client"],
        "baseUrl": ".",
        "paths": {
            "@/*": ["resources/js/*"]
        }
    },
    "include": ["resources/js/**/*.ts", "resources/js/**/*.d.ts", "resources/js/**/*.tsx", "resources/js/**/*.vue"]
}
EOL

# Set up database configuration
echo "Setting up database configuration..."
echo "Please provide your MySQL database credentials:"
read -p "Database name (default: encom): " DB_NAME
DB_NAME=${DB_NAME:-encom}
read -p "Database user (default: root): " DB_USER
DB_USER=${DB_USER:-root}
read -p "Database password: " DB_PASSWORD

# Update .env file
sed -i '' "s/DB_DATABASE=laravel/DB_DATABASE=$DB_NAME/" .env
sed -i '' "s/DB_USERNAME=root/DB_USERNAME=$DB_USER/" .env
sed -i '' "s/DB_PASSWORD=/DB_PASSWORD=$DB_PASSWORD/" .env

# Create database
echo "Creating database..."
mysql -u $DB_USER -p$DB_PASSWORD -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
echo "Running migrations..."
php artisan migrate

# Install Vue.js for frontend
echo "Installing Vue.js and related packages..."
npm install vue@next
npm install --save-dev @vitejs/plugin-vue

# Update vite.config.js to support Vue
cat > vite.config.js << 'EOL'
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
});
EOL

# Create basic TypeScript app entry point
mkdir -p resources/js/components
cat > resources/js/app.ts << 'EOL'
import { createApp } from 'vue';
import App from './components/App.vue';

createApp(App).mount('#app');
EOL

# Create basic Vue component
cat > resources/js/components/App.vue << 'EOL'
<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Encom Dashboard</div>
                    <div class="card-body">
                        Welcome to Encom Smart Home Web App!
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
    name: 'App',
    setup() {
        return {};
    }
});
</script>
EOL

# Update welcome blade to use Vue
cat > resources/views/welcome.blade.php << 'EOL'
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Encom - Smart Home Dashboard</title>
        @vite(['resources/css/app.css', 'resources/js/app.ts'])
    </head>
    <body>
        <div id="app"></div>
    </body>
</html>
EOL

# Install dependencies
echo "Installing npm dependencies..."
npm install

# Build assets
echo "Building assets..."
npm run build

echo
echo "=== Encom project setup complete! ==="
echo
echo "To start the development server, run:"
echo "php artisan serve"
echo
echo "Your Encom Smart Home Web App will be available at: http://localhost:8000"
echo
echo "Next steps:"
echo "1. Implement user authentication (php artisan make:auth)"
echo "2. Create widget models and migrations"
echo "3. Develop the dashboard UI"
echo "4. Implement the widget system"
echo
echo "Refer to the implementation plan for detailed steps."
