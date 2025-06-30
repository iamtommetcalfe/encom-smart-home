#!/bin/bash

# Test Docker Setup for Encom Smart Home Web App

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "Docker is not running. Please start Docker and try again."
    exit 1
fi

# Function to display help
function show_help {
    echo "Test Docker Setup for Encom Smart Home Web App"
    echo "Usage: ./test-docker-setup.sh"
    echo ""
    echo "This script will test the Docker setup by:"
    echo "1. Building and starting the Docker containers"
    echo "2. Running database migrations"
    echo "3. Checking if the application is accessible via the custom hostname"
    echo ""
}

# Display help
show_help

# Ask for confirmation
read -p "Do you want to proceed with the test? (y/n): " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Test cancelled."
    exit 1
fi

# Step 1: Build and start the Docker containers
echo "Step 1: Building and starting the Docker containers..."
./docker-encom.sh build
if [ $? -ne 0 ]; then
    echo "Failed to build Docker containers."
    exit 1
fi

./docker-encom.sh up
if [ $? -ne 0 ]; then
    echo "Failed to start Docker containers."
    exit 1
fi

# Wait for containers to be ready
echo "Waiting for containers to be ready..."
sleep 10

# Step 2: Run database migrations
echo "Step 2: Running database migrations..."
./docker-encom.sh migrate
if [ $? -ne 0 ]; then
    echo "Failed to run database migrations."
    exit 1
fi

# Step 3: Check if the application is accessible via the custom hostname
echo "Step 3: Checking if the application is accessible via the custom hostname..."
response=$(curl -s -o /dev/null -w "%{http_code}" http://encom-smart-home.local)
if [ $response -eq 200 ]; then
    echo "Success! The application is accessible at http://encom-smart-home.local"
else
    echo "Failed to access the application. HTTP response code: $response"
    exit 1
fi

# All tests passed
echo "All tests passed! The Docker setup is working correctly."
exit 0
