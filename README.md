# CDC Task Demo

This is a minimalist backend project demonstrating Change Data Capture (CDC) using PHP 8.3 and PostgreSQL 16. It uses database triggers to automatically log all insertions and updates into an audit table.

## Project Structure

cdc-task-demo/
├── docker-compose.yml (container orchestration)
├── Dockerfile (php-apache configuration)
├── composer.json (autoloading and dependencies)
├── sql/
│   └── 01-init.sql (database schema and cdc triggers)
└── src/
    ├── index.php (api entry point and routing)
    └── Task.php (task model and validation)

## Setup

1. Build and start the containers:
docker compose up --build -d

2. The API will be available at:
http://localhost:8080/

## API Endpoints

### GET /
Returns a list of all tasks in the database.

### POST /
Creates a new task.
Payload: {"title": "Task Name", "status": "open"}

### GET /changes
Returns the full CDC change log captured by the database trigger.
