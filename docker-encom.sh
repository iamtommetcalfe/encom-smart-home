#!/bin/bash

# Encom Docker Helper Script

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "Docker is not running. Please start Docker and try again."
    exit 1
fi

# Function to display help
function show_help {
    echo "Encom Docker Helper Script"
    echo "Usage: ./docker-encom.sh [command]"
    echo ""
    echo "Commands:"
    echo "  up              Start the Docker containers"
    echo "  down            Stop the Docker containers"
    echo "  build           Build the Docker containers"
    echo "  restart         Restart the Docker containers"
    echo "  bash            Open a bash shell in the Laravel container"
    echo "  artisan [cmd]   Run an Artisan command"
    echo "  composer [cmd]  Run a Composer command"
    echo "  npm [cmd]       Run an npm command"
    echo "  vite            Start the Vite development server"
    echo "  migrate         Run database migrations"
    echo "  fresh           Reset the database and run all migrations"
    echo "  seed            Seed the database"
    echo "  test            Run PHPUnit tests"
    echo "  help            Show this help message"
    echo ""
}

# Check if a command was provided
if [ $# -eq 0 ]; then
    show_help
    exit 1
fi

# Process the command
case "$1" in
    up)
        echo "Starting Docker containers..."
        ./vendor/bin/sail up -d
        ;;
    down)
        echo "Stopping Docker containers..."
        ./vendor/bin/sail down
        ;;
    build)
        echo "Building Docker containers..."
        ./vendor/bin/sail build --no-cache
        ;;
    restart)
        echo "Restarting Docker containers..."
        ./vendor/bin/sail down
        ./vendor/bin/sail up -d
        ;;
    bash)
        echo "Opening bash shell in Laravel container..."
        ./vendor/bin/sail bash
        ;;
    artisan)
        shift
        if [ $# -eq 0 ]; then
            echo "Please provide an Artisan command."
            exit 1
        fi
        echo "Running Artisan command: $@"
        ./vendor/bin/sail artisan "$@"
        ;;
    composer)
        shift
        if [ $# -eq 0 ]; then
            echo "Please provide a Composer command."
            exit 1
        fi
        echo "Running Composer command: $@"
        ./vendor/bin/sail composer "$@"
        ;;
    npm)
        shift
        if [ $# -eq 0 ]; then
            echo "Please provide an npm command."
            exit 1
        fi
        echo "Running npm command: $@"
        ./vendor/bin/sail npm "$@"
        ;;
    vite)
        echo "Starting Vite development server..."
        ./vendor/bin/sail npm run dev
        ;;
    migrate)
        echo "Running database migrations..."
        ./vendor/bin/sail artisan migrate
        ;;
    fresh)
        echo "Resetting database and running all migrations..."
        ./vendor/bin/sail artisan migrate:fresh
        ;;
    seed)
        echo "Seeding the database..."
        ./vendor/bin/sail artisan db:seed
        ;;
    test)
        echo "Running PHPUnit tests..."
        ./vendor/bin/sail test
        ;;
    help)
        show_help
        ;;
    *)
        echo "Unknown command: $1"
        show_help
        exit 1
        ;;
esac

exit 0
