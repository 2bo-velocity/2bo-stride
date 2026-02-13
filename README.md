# 2bo Stride Framework

**"Explicit, Lightweight, Production-minded PHP framework"**

2bo Stride is a lightweight, production-ready PHP framework. It emphasizes explicit design principles and high performance, achieving predictable behavior by avoiding Reflection and DI Containers.

## Key Features

- **Ultra Lightweight**: Fast startup time due to no Container/Reflection magic.
- **Production Ready**: Designed with built-in support for DB Master/Slave, Rate Limiting, and Circuit Breaking.
- **Explicit DI**: Eliminates "magic" favoring manual injection for clear dependencies.
- **Straightforward MVC**: Standard structure for Helpers, Repositories, and Services.
- **CLI Integration**: Integrated `stride` command to support development workflows.

## Requirements

- **PHP**: 8.1 or higher
- **Composer** (for dependency resolution and autoloader generation)
- **Extensions**: pdo, pdo_mysql, mbstring (recommended)

## Installation

Run the following commands in your project root:

```bash
# Install dependencies
composer install

# Create environment configuration
cp .env.example .env
```

Edit the `.env` file to configure your database connection and other settings.

## Directory Structure

```
2bo-stride/
├── app/            # Application code (Controllers, Services, etc.)
├── bootstrap/      # Bootstrapping scripts
├── commands/       # CLI command definitions
├── config/         # Configuration files
│   ├── app.php     # General app settings (name, env, providers)
│   ├── db.php      # Database connection (master/slave)
│   ├── batch.php   # Batch job registry
│   ├── worker.php  # Queue worker settings
│   ├── features.php# Feature flags
│   ├── cache.php   # Cache settings
│   └── schema.php  # Schema version settings
├── public/         # Public directory (index.php)
├── resources/      # View templates (views/)
├── routes/         # Route definitions (web.php, api.php)
├── storage/        # Logs, cache, and session storage
├── system/         # Framework core (Stride Core)
└── vendor/         # Composer dependencies
```

## Usage

### Starting the Web Server

You can use the PHP built-in server for testing.

```bash
php -S localhost:8000 -t public
php -S localhost:8000 -t public
```

Open `http://localhost:8000` in your browser.

### Version Check

You can check the framework version and environment details via CLI.

```bash
# SImple version check
php stride --version

# Full version details
php stride version:full
```

Open `http://localhost:8000` in your browser.

### CLI Tools

Use the `stride` script located in the project root.

```bash
# List available commands
./stride list

# Create a new controller
./stride make:controller UserController

# Cache configuration (for production)
./stride config:cache

# Run migrations
./stride migrate

# Cache configuration (faster loading)
./stride config:cache

# Cache routes (faster registration)
./stride route:cache

# Enable Deploy Safe Mode (pauses batches/queues)
./stride deploy:on

# Disable Deploy Safe Mode
./stride deploy:off

# Enable Maintenance Mode (web only)
./stride maintenance:on

# Disable Maintenance Mode
./stride maintenance:off

# Check Maintenance Mode Status
./stride maintenance:status

# Clear configuration cache
./stride config:clear

# Check DB Schema Version
./stride schema:version

# Check DB Replica Health
./stride replica:check

# Set Blue/Green Deployment (blue or green)
./stride bluegreen:set blue

# Check Blue/Green Status
./stride bluegreen:status
```

### Development Guide

#### 1. Routing

Define routes in `routes/web.php`.

```php
use App\Controllers\HomeController;

/** @var Router $router */
$router = app()->router;

// Route to Class & Method
$router->get('/', [HomeController::class, 'index']);

// Route to Closure
$router->get('/hello', function() {
    return 'Hello Stride!';
});
```

#### 2. Controllers

Place controllers in `app/Controllers/`. They should accept a `Request` object and return a `Response` object.

```php
namespace App\Controllers;

use Stride\Core\Http\Request;
use Stride\Core\Http\Response;

class UserController
{
    public function index(Request $request): Response
    {
        // Render a view template using the view() helper
        return new Response(view('users/index', ['users' => []]));
    }
}
```

#### 3. Middleware

Middleware provides a convenient mechanism for inspecting and filtering HTTP requests entering your application.

**Global Middleware (`system/src/Core/App.php`):**
Runs on every request.

**Route Middleware (`routes/web.php`):**
Assignable to specific routes.

```php
// Define middleware
class AuthMiddleware {
    public function __invoke(Request $request, callable $next) {
        if (!Auth::check()) {
            return new Response("Unauthorized", 401);
        }
        return $next($request);
    }
}

// Assign to route
$router->get('/dashboard', [DashboardController::class, 'index'])
       ->middleware([AuthMiddleware::class]);
```

**Built-in Middleware:**
- **`Stride\Core\Middleware\MaintenanceMiddleware`**: Checks for maintenance mode (enabled by default in global stack).
- **`Stride\Core\Middleware\LoggingMiddleware`**: Logs request/response details (method, path, status, duration).

#### 3. Views

Place view files in `resources/views/`. The base layout is `resources/views/layout/main.php`.
Use the `h()` helper function to escape output for XSS protection.

```php
<!-- resources/views/pages/home.php -->
<h1><?= h($title) ?></h1>

<!-- Include a partial view -->
<?php partial('header', ['active' => 'home']) ?>

<form method="POST" action="/submit">
    <!-- CSRF Protection -->
    <?= csrf_field() ?>
    <input type="text" name="name">
    <button>Submit</button>
</form>

<!-- Pagination -->
<?= paginate($paginator) ?>
```

#### 4. Database

Configure database settings in `config/db.php`. Access the database via `ConnectionManager`.
Stride supports Master/Slave configurations, using Slave (Read) by default and switching to Master (Write) when needed.

```php
// Reading (Slave)
$users = app()->db->slave()->query("SELECT * FROM users")->fetchAll();

// Writing (Master) via Query Builder
$db = app()->db->query();

// Insert
$db->table('users')->insert([
    'name' => 'Alice',
    'email' => 'alice@example.com'
]);

// Update
$db->table('users')->where('id', '=', 1)->update([
    'name' => 'Alice Smith'
]);

// Delete
$db->table('users')->where('id', '=', 1)->delete();
```

#### 5. Integrating External Libraries

2bo Stride is designed to easily integrate with external libraries using the Service Provider pattern. Here is an example flow for adding Redis and Guzzle HTTP Client.

**1. Install Libraries via Composer**

```bash
# Redis Library
composer require predis/predis

# Guzzle HTTP Client
composer require guzzlehttp/guzzle
```

This installs the libraries into `vendor/`.

**2. Create Service Providers**

Create provider classes to register the services into the `App` container.

`app/Providers/RedisServiceProvider.php`:
```php
<?php
namespace App\Providers;

use Stride\Core\Container\ServiceProvider;
use Stride\Core\App;
use Predis\Client;

class RedisServiceProvider implements ServiceProvider {
    public function register(App $app): void {
        $app->redis = new Client([
            'scheme' => 'tcp',
            'host'   => env('REDIS_HOST', '127.0.0.1'),
            'port'   => env('REDIS_PORT', 6379),
        ]);
    }
}
```

`app/Providers/HttpClientProvider.php`:
```php
<?php
namespace App\Providers;

use Stride\Core\Container\ServiceProvider;
use Stride\Core\App;
use GuzzleHttp\Client;

class HttpClientProvider implements ServiceProvider {
    public function register(App $app): void {
        $app->http = new Client([
            'base_uri' => 'https://api.example.com',
            'timeout'  => 2.0,
        ]);
    }
}
```

**3. Register Providers**

Add the providers to `bootstrap/app.php` so they are loaded on startup.

```php
// bootstrap/app.php
$app->register(new App\Providers\RedisServiceProvider());
$app->register(new App\Providers\HttpClientProvider());

// Note: Core providers like DatabaseProvider are registered automatically.
```

**4. Usage**

You can now access these services globally via the `app()` helper or `App` instance.

```php
// Redis Usage
app()->redis->set('key', 'value');
$value = app()->redis->get('key');

// HTTP Client Usage
$response = app()->http->get('/endpoint');
$data = json_decode($response->getBody(), true);
```

**Key Benefits:**
- **Lightweight Core**: The framework remains lean; you only add what you need.
- **Explicit Dependencies**: Dependencies are clearly defined in Service Providers.
- **Unified Pattern**: Any PHP library (Monolog, Carbon, etc.) can be integrated this way.
- **Testable**: Services can be easily mocked or replaced for testing.

### Batch System

This document guides you through the newly implemented Batch System for 2bo Stride Framework.

#### 1. Creating a New Batch Job

Create a new class in `app/Batch/` that implements `Stride\Core\Batch\BatchJob`.

```php
<?php

namespace App\Batch;

use Stride\Core\Batch\BatchJob;
use Stride\Core\Support\Logger;
use Stride\Core\Support\ExitCode;

class UserCleanupBatch implements BatchJob
{
    public function handle(): int
    {
        Logger::info("Starting user cleanup...");
        
        // Your logic here
        
        Logger::info("User cleanup finished.");
        
        return ExitCode::SUCCESS;
    }
}
```

#### 2. Registering the Batch

Add your batch class to `config/batch.php`.

```php
<?php

return [
    'example' => App\Batch\ExampleBatch::class,
    'user-cleanup' => App\Batch\UserCleanupBatch::class, // Add this line
];
```

#### 3. Listing Available Batches

Run the following command to see all registered batches:

```bash
php stride batch:list
```

**Output:**
```
Available batches:
  - example
  - user-cleanup
```

#### 4. Running a Batch

Execute a specific batch by its name:

```bash
php stride batch:run user-cleanup
```

**Output:**
```
[INFO] Starting user cleanup...
[INFO] User cleanup finished.
[INFO] Batch success {"job":"App\\Batch\\UserCleanupBatch","ms":...,"exit_code":0}
```

#### 5. Operational Features

**Maintenance Mode**
If the application is in maintenance mode (`APP_MAINTENANCE=true` in `.env`), batch execution will be skipped ensuring system stability during updates.

**Deploy Safe Mode**
**Deploy Safe Mode**
During deployment (`APP_SAFE_MODE=true`), batch execution is paused to prevent data inconsistency.
You can toggle this mode via CLI:
```bash
php stride deploy:on   # Enable Safe Mode
php stride deploy:off  # Disable Safe Mode
```

#### 6. Verification Results

We have verified the system with an example batch:

**List Command:**

```bash
php stride batch:list
```
Result: Successfully listed `example` batch.

**Run Command:**

```bash
php stride batch:run example
```
Result: Successfully executed `ExampleBatch` and logged output.

### Queue System

Stride provides a lightweight queue system with unique job protection.

#### Worker Configuration

Configured in `config/worker.php`.

```php
return [
    'max_jobs' => 1000,
    'max_memory_mb' => 256,
    'max_runtime_sec' => 3600,
];
```

#### Unique Jobs

Prevent duplicate jobs using `UniqueJob`.

```php
use Core\Queue\UniqueJob;

// Only one welcome email per user every 10 minutes
UniqueJob::push(
    "email:welcome:{$userId}",
    new SendWelcomeMail($userId),
    600
);
```

### Kubernetes Support

Stride is constructed with containerized environments in mind.

#### Health Checks

Liveness and Readiness probes are available at:

- `/health/live`: Returns 200 OK.
- `/health/ready`: Checks DB, Redis, and Safe Mode status.

#### Graceful Shutdown

Workers handle `SIGTERM` signals to finish processing current jobs before exiting.

```php
// In your worker loop
if (SignalManager::shouldShutdown()) {
    Logger::notice("Worker graceful shutdown");
    break;
}
```

#### Stateless Guard

In production, Stride warns about stateful configurations (file session, local storage) that are not suitable for containers.

### Advanced Operations

#### Feature Flags

Control feature rollout via `config/features.php`.

```php
if (app()->featureFlags->isEnabled('new_checkout')) {
    // Show new checkout
}
```

#### Circuit Breaker

Prevent cascading failures when external services are down.

```php
try {
    $result = app()->circuitBreaker->call('payment_api', function() {
        return $http->get('/charge');
    });
} catch (RuntimeException $e) {
    // Service unavailable, show fallback
}
```

#### Metrics

Prometheus-compatible metrics are available at `/metrics`.

#### Database Safety Guards

**Zero-downtime Migrations**

Stride prevents dangerous operations during migrations.

```php
// Fails if SQL contains DROP COLUMN, etc.
MigrationGuard::check($sql);

// Waits if replica lag is high
if (!ReplicaHealth::isSafe()) {
    exit(1);
}
```

### Core Framework Features

#### Security

Stride provides standard security helpers.

- **CSRF**: `Csrf::token()` and `Csrf::check($token)`
- **Session**: `Session::get($key)` and `Session::set($key, $value)`
- **Auth**: Simple stateful authentication with `Auth::login($uid)` and `Auth::check()`

#### Resilience

Built-in resilience patterns help build robust applications.

- **Rate Limiting**: `app()->rateLimiter->check($key, $limit, $window)`
- **Circuit Breaker**: `app()->circuitBreaker->call($key, $callback)`

#### Operations

Operational features for production visibility and control.

- **Metrics**: `app()->metrics->inc($name)`
- **Request ID**: `RequestId::get()` for tracing.
- **Blue/Green**: `app()->blueGreen->isBlue()` helper.
- **Logging**: `Logger::info($message, $context)` logs to `storage/logs/app.log` and stderr.

#### Error Handling

Global exception handling is enabled by default in `App`.

- **Development**: Shows detailed stack traces (`APP_DEBUG=true`).
- **Production**: Returns generic 500 error pages or JSON responses.

#### Migrations

Manage database schema changes with version control.

- **Create**: Create migration classes extending `Stride\Core\Migration\Migration`.
- **Run**: `stride migrate` executes pending migrations safely.
- **Guards**: `MigrationGuard` prevents dangerous operations (e.g., `DROP COLUMN`) for zero-downtime deployment safety.

#### Utilities

- **Validation**: `Validator` class for array validation.
- **Pagination**: `Paginator` class.
- **Documentation**: Run `php stride docs` to generate API references.

#### Helpers

Available global helper functions:

- `app()`: Get the application instance.
- `config($key, $default)`: Get configuration value.
- `env($key, $default)`: Get environment variable.
- `view($file, $vars)`: Render a view.
- `h($str)`: Escape string for HTML (XSS protection).
- `asset($path)`: Get asset URL (supports versioning via `app.asset_version`).
- `json($data, $status)`: Return JSON response.
- `csrf_token()`, `csrf_field()`: CSRF protection helpers.
- `paginate($paginator)`: Render pagination links.
- `partial($name, $data)`: Include a view partial.

#### Deployment Safety

**Auto Deploy Guard**:
When running migrations (`stride migrate`), the system automatically checks if `APP_SAFE_MODE` is enabled. If not, it will temporarily enable it to ensure data consistency and disable it afterwards, unless it was already enabled.

**EnvWriter**:
Internal utility used by CLI commands to safely modify `.env` files (e.g. for toggling maintenance or safe mode).

### Key Environment Variables

Ensure these are set in your `.env` file:

#### Application
- **`APP_NAME`**: Application name.
- **`APP_ENV`**: `local` or `production`.
- **`APP_DEBUG`**: `true` for stack traces, `false` for generic errors.
- **`APP_URL`**: Application URL.
- **`APP_KEY`**: Application key.
- **`APP_MAINTENANCE`**: `true` to enable maintenance mode.
- **`APP_SAFE_MODE`**: `true` to pause batches/workers during deployment.
- **`APP_BLUEGREEN`**: Current active environment (`blue` or `green`).

#### Asset
- **`ASSET_URL`**: Asset URL.
- **`ASSET_VERSION`**: Appended to asset URLs for cache busting (e.g. `?v=1.0`).

#### Database
- **`DB_CONNECTION`**: Database connection type (`mysql`, `pgsql`, etc.).
- **`DB_HOST`**: Database host.
- **`DB_PORT`**: Database port.
- **`DB_DATABASE`**: Database name.
- **`DB_USERNAME`**: Database username.
- **`DB_PASSWORD`**: Database password.
- **`DB_SLAVE_HOST_1`**: Database slave host 1.
- **`DB_SLAVE_PORT_1`**: Database slave port 1.
- **`DB_SLAVE_WEIGHT_1`**: Database slave weight 1.

#### Redis
- **`REDIS_HOST`**: Redis host.
- **`REDIS_PASSWORD`**: Redis password.
- **`REDIS_PORT`**: Redis port.

#### Cache
- **`CACHE_DRIVER`**: Cache driver (`file` or `redis`).

#### Worker
- **`WORKER_MAX_JOBS`**: Maximum number of jobs per worker.
- **`WORKER_MAX_MEMORY_MB`**: Maximum memory usage per worker in MB.
- **`WORKER_MAX_RUNTIME_SEC`**: Maximum runtime per worker in seconds.

#### Schema
- **`SCHEMA_EXPECTED_VERSION`**: Expected schema version.

## License

MIT License

Copyright (c) 2026 2bo
