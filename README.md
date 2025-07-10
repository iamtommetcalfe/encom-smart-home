# Encom Home Dashboard

Encom is a modern home dashboard application designed to be served on your home network. It provides a centralized interface for monitoring various aspects of your home, including weather information, bin collection schedules, and plant watering reminders.

## Features

- **Single Page Application (SPA)**: Smooth, app-like experience with no page reloads
- **Responsive Design**: Works on desktop, tablet, and mobile devices
- **Dark Mode Support**: Easy on the eyes during nighttime use
- **Multiple Widgets**:
  - **Weather Widget**: Shows current weather and forecast for your location
  - **Bin Collection Widget**: Tracks and reminds you of upcoming bin collection dates
  - **Plant Watering Widget**: Provides watering recommendations based on current and forecasted weather conditions

## Technology Stack

- **Backend**: Laravel (PHP framework)
- **Frontend**: Vue.js 3 with TypeScript
- **State Management**: Pinia
- **Styling**: Tailwind CSS
- **Database**: MySQL
- **Development Environment**: Docker with Laravel Sail

## Installation and Setup

### Prerequisites

Before you begin, ensure you have the following installed on your system:

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/) (usually included with Docker Desktop)
- Git

### Installation Steps

1. **Clone the repository**:
   ```bash
   git clone git@github.com:iamtommetcalfe/encom-home.git
   cd encom
   ```

2. **Copy the environment file**:
   ```bash
   cp .env.example .env
   ```

3. **Make the helper script executable**:
   ```bash
   chmod +x docker-encom.sh
   ```

4. **Build and start the Docker containers**:
   ```bash
   ./docker-encom.sh build
   ./docker-encom.sh up
   ```

5. **Install PHP dependencies**:
   ```bash
   ./docker-encom.sh composer install
   ```

6. **Generate application key**:
   ```bash
   ./docker-encom.sh artisan key:generate
   ```

7. **Run database migrations**:
   ```bash
   ./docker-encom.sh migrate
   ```

8. **Install Node.js dependencies**:
   ```bash
   ./docker-encom.sh npm install
   ```

9. **Build frontend assets**:
   ```bash
   ./docker-encom.sh npm run build
   ```

### Custom Hostname Setup

The application can be configured to use the custom hostname `encom-home.local`. To make this work on your local machine:

#### On macOS and Linux:

1. Edit the hosts file (requires administrator privileges):
   ```bash
   sudo nano /etc/hosts
   ```

2. Add the following line:
   ```
   127.0.0.1    encom-home.local
   ```

3. Save the file (in nano: Ctrl+O, Enter, Ctrl+X).

#### On Windows:

1. Open Notepad as Administrator.
2. Open the file `C:\Windows\System32\drivers\etc\hosts`.
3. Add the following line:
   ```
   127.0.0.1    encom-home.local
   ```
4. Save the file.

After updating your hosts file, you should be able to access the application at http://encom-home.local in your web browser.

## Development Workflow

### Helper Script Commands

We've created a helper script (`docker-encom.sh`) to make working with Docker easier:

- `./docker-encom.sh up`: Start the Docker containers
- `./docker-encom.sh down`: Stop the Docker containers
- `./docker-encom.sh build`: Build the Docker containers
- `./docker-encom.sh restart`: Restart the Docker containers
- `./docker-encom.sh bash`: Open a bash shell in the Laravel container
- `./docker-encom.sh artisan [cmd]`: Run an Artisan command
- `./docker-encom.sh composer [cmd]`: Run a Composer command
- `./docker-encom.sh npm [cmd]`: Run an npm command
- `./docker-encom.sh vite`: Start the Vite development server
- `./docker-encom.sh migrate`: Run database migrations
- `./docker-encom.sh fresh`: Reset the database and run all migrations
- `./docker-encom.sh seed`: Seed the database
- `./docker-encom.sh test`: Run PHPUnit tests
- `./docker-encom.sh help`: Show the help message

### Typical Development Workflow

1. **Start the Docker containers**:
   ```bash
   ./docker-encom.sh up
   ```

2. **Start the Vite development server**:
   ```bash
   ./docker-encom.sh vite
   ```

3. **Make changes to your code**. The changes will be automatically reflected in the running application.

4. **Run tests to ensure everything is working correctly**:
   ```bash
   ./docker-encom.sh test
   ```

5. **When you're done, stop the Docker containers**:
   ```bash
   ./docker-encom.sh down
   ```

## Database Management

The MySQL database is running in a Docker container and is accessible from the Laravel container. You can also connect to it from your host machine using the following credentials:

- **Host**: 127.0.0.1
- **Port**: 3306
- **Database**: encom
- **Username**: sail
- **Password**: password

You can change these credentials in the `.env` file.

## Testing the Docker Setup

We've included a test script to verify that the Docker setup is working correctly:

```bash
chmod +x test-docker-setup.sh
./test-docker-setup.sh
```

The script will:
1. Build and start the Docker containers
2. Run database migrations
3. Check if the application is accessible via the custom hostname

If all tests pass, you should see a success message.

## Plant Watering Logic

The Plant Watering Widget provides recommendations on when to water your plants based on current and forecasted weather conditions. The logic takes into account:

1. **Current Precipitation**: If it's currently raining heavily (precipitation > 5mm), the widget will recommend waiting to water your plants.

2. **Forecasted Precipitation**: If significant rain (> 10mm) is forecasted in the next 3 days, the widget will recommend waiting to water your plants.

3. **Current Temperature**: If it's very hot (temperature > 28°C), the widget will recommend watering your plants immediately to prevent dehydration.

4. **Dry Conditions**: If it's currently dry (no precipitation) and little rain (< 5mm) is expected in the forecast, the widget will recommend watering your plants.

The widget displays a clear recommendation (Water Now or Wait to Water) along with an explanation of the reasoning behind the recommendation. It also shows a 3-day weather forecast to help you plan your watering schedule.

## Troubleshooting

### Container Won't Start

If a container won't start, check the Docker logs:

```bash
docker-compose logs
```

### Database Connection Issues

If you're having issues connecting to the database, make sure the MySQL container is running and the database credentials in the `.env` file are correct.

### Port Conflicts

If you're seeing port conflicts, you might have another service running on the same port. You can change the port mappings in the `docker-compose.yml` file or stop the conflicting service.

### Vite Hot Module Replacement (HMR) Issues

If changes to your frontend files are not automatically reflected in the browser when using the Vite development server, ensure that:

1. The Vite server is properly configured for Docker in the `vite.config.js` file by adding:
   ```
   server: {
     host: '0.0.0.0',
     hmr: {
       host: '0.0.0.0',
     },
     watch: {
       usePolling: true,
       interval: 1000,
     },
   },
   ```

2. The Docker container has the correct volume mapping in `docker-compose.yml`:
   ```
   volumes:
     - '.:/var/www/html'
   ```

3. Restart the Vite development server after making these changes:
   ```
   ./docker-encom.sh down
   ./docker-encom.sh up
   ./docker-encom.sh vite
   ```
