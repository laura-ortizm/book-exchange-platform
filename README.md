# Book Exchange Platform

A circular-economy web platform for exchanging second-hand books between users, built with Laravel, MariaDB, and Bootstrap 5.

## Features

- **Public catalog** — browse and search books by title, author, or category
- **User area** — publish books and manage exchange requests via an inbox
- **Admin area** — manage categories and resolve disputes between users

## Tech stack

| Layer | Choice |
|---|---|
| Backend | Laravel (PHP 8.5) |
| Database | MariaDB 11 |
| CSS Framework | Bootstrap 5 |
| Local dev | Laravel Sail (Docker) |

## Architecture

### Local development

```
Browser
   │  HTTP
   ▼
┌──────────────────────────────────────────┐
│  Docker Compose (Laravel Sail)           │
│                                          │
│  ┌─────────────────────┐                 │
│  │  app  :80           │                 │
│  │  PHP 8.5 + Apache   │                 │
│  │  Laravel 13         │◄── your code    │
│  └────────┬────────────┘    (bind mount) │
│           │                              │
│  ┌────────▼────────────┐                 │
│  │  mariadb  :3306     │                 │
│  │  MariaDB 11         │◄── sail-mariadb │
│  └─────────────────────┘    (volume)     │
│                                          │
│  ┌─────────────────────┐                 │
│  │  phpmyadmin  :8080  │                 │
│  └─────────────────────┘                 │
└──────────────────────────────────────────┘
```

### Application layers

```
Request
   │
   ▼
routes/web.php          ← decides which controller handles the request
   │
   ▼
Middleware              ← auth check, admin check, guest guard
   │
   ▼
Controller              ← validates input, calls models, returns response
   │
   ▼
Eloquent Model          ← reads/writes MariaDB via PDO
   │
   ▼
Blade View              ← renders HTML with Bootstrap 5
```

### Data storage

| Data | Where |
|---|---|
| Users, books, categories, exchanges, disputes | MariaDB (`sail-mariadb` Docker volume) |
| Sessions and cache | MariaDB (`sessions` / `cache` tables) |
| Book cover images | `storage/app/public/covers/` |
| App config and secrets | `.env` (local only, never in git) |

### Future deployment (AWS)

The same `compose.yaml` can be deployed to an EC2 instance with no code changes —
only the `.env` values differ between local and production.

## Requirements

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/)

## Getting started

### 1. Clone the repository

```bash
git clone <repo-url>
cd book-exchange-platform
```

### 2. Configure environment

```bash
cp .env.example .env
```

Open `.env` and fill in:

```
DB_PASSWORD=          # any password you like for the local MariaDB container
ADMIN_SEED_PASSWORD=  # password for the dev admin account (e.g. admin123)
```

> Each teammate sets their own values — `.env` is never committed to git.

### 3. Install dependencies

```bash
docker run --rm -v "$(pwd)":/app -w /app composer:latest install --no-interaction
```

### 4. Start the stack

```bash
./vendor/bin/sail up --build
```

On first run, Sail builds the app image and MariaDB initialises automatically.

| Service | URL |
|---|---|
| Application | http://localhost |
| phpMyAdmin | http://localhost:8080 |

### 5. Generate application key

```bash
./vendor/bin/sail artisan key:generate
```

### 6. Run migrations and seed

```bash
./vendor/bin/sail artisan migrate --seed
```

This creates all tables and inserts the initial categories, an admin account,
and a sample user. Each teammate runs this once on their own machine —
**no shared database required**.

### 7. Seed accounts

| Role | Username | Email | Password |
|---|---|---|---|
| Admin | `admin` | admin@bookexchange.local | *(your `ADMIN_SEED_PASSWORD`)* |
| User | `chrisvega` | chrisvega@ugr.es | `user123` |
| User | `lauraortiz` | lauraortiz@ugr.es | `user123` |
| User | `pablosoriano` | pablosoriano@ugr.es | `user123` |

Regular users can register themselves via `/register`.

## Project structure

```
book-exchange-platform/
├── app/
│   ├── Http/
│   │   ├── Controllers/    # Route handlers
│   │   └── Middleware/     # Auth and role guards
│   └── Models/             # Eloquent models: User, Book, Category, Exchange, Dispute
├── database/
│   ├── migrations/         # Table definitions (replaces schema.sql)
│   └── seeders/            # Initial data: categories, users, sample books
├── resources/
│   └── views/              # Blade templates
│       └── layouts/        # Shared header, footer, sidebar
├── routes/
│   └── web.php             # All application routes
├── public/                 # Web root (index.php, assets)
├── storage/app/public/     # Uploaded book cover images
├── compose.yaml            # Sail Docker Compose (app + mariadb + phpmyadmin)
└── como_se_hizo.pdf        # Project report (not tracked in git)
```

## User roles

| Role | Capabilities |
|---|---|
| Guest | Browse catalog, search, view book detail, contact |
| User | All guest capabilities + publish books, manage exchange inbox |
| Admin | All user capabilities + manage categories, resolve disputes |

## Useful Sail commands

```bash
# Stop the stack
./vendor/bin/sail down

# Reset database (re-runs all migrations and seeders)
./vendor/bin/sail artisan migrate:fresh --seed

# Open a shell inside the app container
./vendor/bin/sail shell

# Run tests
./vendor/bin/sail artisan test
```

## Accessing from another device (phone, tablet)

`localhost` only resolves on your own machine. To test on another device, expose the local server with [ngrok](https://ngrok.com) using a persistent static domain:

```bash
ngrok http --domain=your-subdomain.ngrok-free.app 80
```

Set `APP_URL` in `.env` to your static domain — do this once, it never changes:

```
APP_URL=https://your-subdomain.ngrok-free.app
```

Then follow the normal getting started steps. No extra restart is needed.

> The ngrok domain must match `APP_URL` exactly — otherwise Laravel generates broken asset URLs and the page loads without styles.

## Resetting the stack completely

To wipe all containers and volumes:

```bash
./vendor/bin/sail down -v
./vendor/bin/sail up --build
./vendor/bin/sail artisan migrate --seed
```
