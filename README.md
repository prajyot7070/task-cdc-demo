# 🚀 CDC Task Demo

A **minimal, production-ready** PHP + PostgreSQL + Docker project that demonstrates Change-Data-Capture (CDC) using native PostgreSQL triggers. Built to cover a full syllabus of Docker, PHP, advanced database, and unit-testing concepts — all in a single repository.

---

## 📐 Syllabus Coverage

| Topic | File(s) |
|---|---|
| **Docker – Dockerfile** | `Dockerfile` |
| **Docker – docker-compose + networking** | `docker-compose.yml` (service `web` & `db` on `cdc-net`) |
| **Docker – best practices** | Non-root user, health-check, `.dockerignore`, env vars |
| **Docker – debugging** | `docker compose logs web`, `exec` tips in README |
| **PHP – syntax & forms** | `src/public/index.php` (POST forms, PRG pattern) |
| **PHP – sessions** | `src/public/index.php` (flash messages via `$_SESSION`) |
| **PHP – regex** | `src/App/Models/Task.php` → `validateTitle()` |
| **PHP – namespaces** | `namespace App\Models;` in `Task.php` |
| **PHP – magic methods** | `__construct`, `__toString`, `__get` in `Task.php` |
| **PHP – Composer + autoload** | `composer.json` (PSR-4), `vendor/autoload.php` |
| **PHP – API** | `src/public/api.php` (JSON REST endpoints) |
| **Advanced DB – PostgreSQL triggers (CDC)** | `sql/01-init.sql` → `log_cdc()` + `tasks_cdc_trigger` |
| **Advanced DB – SQL functions** | `sql/01-init.sql` → `get_latest_changes(n)` |
| **Advanced DB – PHP DB integration** | `PDO` + `pdo_pgsql` in `api.php` and `index.php` |
| **ODBC integration** | `src/public/odbc_demo.php` + Dockerfile comments |
| **Unit Testing – PHPUnit** | `src/tests/TaskTest.php`, `phpunit.xml` |

---

## ⚡ Quick Start

```bash
# 1. Clone the repo
git clone https://github.com/yourname/cdc-task-demo.git
cd cdc-task-demo

# 2. Copy environment config
cp .env.example .env
# Optionally edit .env to change credentials/ports

# 3. Build and start containers
docker compose up --build

# 4. Open the app
open http://localhost:8080
```

> **First run** will automatically execute `sql/01-init.sql`, creating the schema, CDC trigger, and seed tasks.

---

## 🗂️ File Structure

```
cdc-task-demo/
├── docker-compose.yml         # Orchestration: web + db on cdc-net
├── Dockerfile                 # php:8.3-apache + pdo_pgsql + Composer
├── .env.example               # Environment variable template
├── .dockerignore              # Excludes vendor/, .env from image
├── .gitignore
├── composer.json              # PSR-4 autoload + PHPUnit dev-dep
├── phpunit.xml                # PHPUnit 11 config
├── sql/
│   └── 01-init.sql            # Schema, CDC trigger, helper SQL function
├── src/
│   ├── public/
│   │   ├── index.php          # Bootstrap UI (sessions, forms, task list)
│   │   ├── api.php            # JSON REST API (PDO, routing, validation)
│   │   └── odbc_demo.php      # ODBC syntax demo page
│   ├── App/
│   │   └── Models/
│   │       └── Task.php       # Namespace, magic methods, regex validation
│   └── tests/
│       └── TaskTest.php       # PHPUnit test class
└── README.md
```

---

## 🔬 How to Test CDC (Change-Data-Capture)

1. Open **http://localhost:8080**
2. Create a task using the form
3. Change its status using the inline dropdown
4. Delete a task
5. Click **"CDC Changes"** in the navbar — each action appears as a row with `INSERT`, `UPDATE`, or `DELETE` and the full `old_data` / `new_data` as JSONB

You can also hit the API directly:
```bash
# All recent changes
curl http://localhost:8080/api.php/changes | jq

# Changes since a specific timestamp
curl "http://localhost:8080/api.php/changes?since=2024-01-01T00:00:00" | jq
```

---

## 🧪 Running PHPUnit Tests

```bash
# Run all tests inside the web container
docker compose exec web vendor/bin/phpunit

# Run with verbose output
docker compose exec web vendor/bin/phpunit --testdox
```

Expected output:
```
CDC Task Demo Tests
  ✔ Constructor sets properties
  ✔ Constructor with empty array uses defaults
  ✔ To string format
  ✔ Get returns correct value
  ✔ Get throws for unknown property
  ✔ Validate title with valid simple title
  ...
```

---

## 🐞 Docker Debugging

```bash
# View logs for all services
docker compose logs -f

# View only web (Apache/PHP) logs
docker compose logs -f web

# Shell into the PHP container
docker compose exec web bash

# Shell into PostgreSQL
docker compose exec db psql -U cdc_user -d cdc_demo

# Check the CDC trigger in PSQL
docker compose exec db psql -U cdc_user -d cdc_demo \
  -c "SELECT * FROM change_log ORDER BY changed_at DESC LIMIT 10;"

# Rebuild image from scratch (clears cache)
docker compose down && docker compose up --build --force-recreate
```

---

## 🌐 API Endpoints

| Method | URL | Description |
|--------|-----|-------------|
| `GET`  | `/api.php/tasks` | List all tasks |
| `POST` | `/api.php/tasks` | Create task (JSON body) |
| `PUT`  | `/api.php/tasks/{id}` | Update task fields |
| `DELETE` | `/api.php/tasks/{id}` | Delete task |
| `GET`  | `/api.php/changes` | All CDC change-log entries |
| `GET`  | `/api.php/changes?since=YYYY-MM-DDTHH:MM:SS` | Changes after timestamp |

**Example: Create a task via API**
```bash
curl -X POST http://localhost:8080/api.php/tasks \
  -H "Content-Type: application/json" \
  -d '{"title":"Learn Docker","description":"Study Dockerfile best practices","status":"open"}'
```

---

## 🐳 Docker Hub

```bash
# Build and tag
docker build -t yourname/cdc-demo:latest .

# Push to Docker Hub
docker push yourname/cdc-demo:latest

# Pull and run (standalone, still needs a running Postgres)
docker run -p 8080:80 \
  -e DB_HOST=host.docker.internal \
  -e DB_NAME=cdc_demo \
  -e DB_USER=cdc_user \
  -e DB_PASSWORD=secret123 \
  yourname/cdc-demo:latest
```

---

## 📸 Screenshots

> _Run the project locally and add screenshots here._

| Task Manager UI | CDC Change Log | API Response |
|---|---|---|
| _(screenshot)_ | _(screenshot)_ | _(screenshot)_ |

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| Language | PHP 8.3 |
| Web Server | Apache 2 (mod_rewrite) |
| Database | PostgreSQL 16 |
| Containerisation | Docker + Docker Compose |
| Testing | PHPUnit 11 |
| UI | Bootstrap 5 + Bootstrap Icons |
| DB Driver | PDO + pdo_pgsql |

---

## 📄 License

MIT © 2024
