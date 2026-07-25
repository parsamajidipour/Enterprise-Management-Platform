# Enterprise Management Platform

A full-stack field operations platform for managing work orders, asset inspections, and technician dispatch — built with a Laravel API, a Nuxt.js admin console, and a Flutter mobile app for field technicians.

> **Portfolio project.** This started as a commercial proposal that didn't move forward, so it's now maintained as a public showcase of the architecture and feature set. All client-specific branding, data, and credentials have been removed or replaced with generic placeholders — nothing here reflects a real deployment or organization.

## What it does


- **Work order lifecycle** — creation, assignment, technician dispatch, status transitions (accepted → on the way → arrived → in progress → completed), all logged to a timeline.
- **Technician dispatch** — recommends technicians by availability, distance, and current workload; blocks duplicate assignments.
- **Asset & inspection management** — asset registry with health scores, categories, and QR/barcode lookup; configurable inspection form templates.
- **Field mobile app** — technicians see only their assigned jobs, execute the job lifecycle, capture GPS location, and upload photo evidence.
- **CMMS integration layer** — an adapter pattern for pulling work orders from an external CMMS and pushing status/completion back, with a fake in-memory CMMS store used for demos.

## Project Structure

```
├── backend/    # Laravel 10 API (work orders, assets, dispatch, CMMS adapter)
├── frontend/   # Nuxt 3 admin console (dispatch board, integrations, settings)
├── mobile/     # Flutter app for field technicians
└── docker-compose.yml
```

## Tech Stack

| Layer    | Stack                                   |
|----------|------------------------------------------|
| Backend  | Laravel 10, MySQL                        |
| Frontend | Nuxt 3, Vue 3                            |
| Mobile   | Flutter                                  |
| Infra    | Docker Compose, Nginx reverse proxy      |

## Running Locally

### Prerequisites

- Docker Engine 24+
- Docker Compose v2+

### Setup

```bash
cp backend/.env.docker.example backend/.env   # fill in APP_KEY, DB credentials, etc.
docker compose up -d --build
docker compose exec backend php artisan migrate:fresh --seed
docker compose exec backend php artisan storage:link
```

For local development with direct ports (`3000`, `8010`, `8090`):

```bash
docker compose -f docker-compose.dev.yml up -d --build
```

### Start / Stop

```bash
docker compose up -d              # start all services
docker compose logs -f backend    # tail logs
docker compose down               # stop
docker compose down -v            # stop and reset the database
```

## Demo Data

Reset the database and seed demo users, assets, and fake CMMS work orders:

```bash
docker compose exec backend php artisan demo:reset
# or, with a pre-assigned mobile job:
docker compose exec backend php artisan demo:reset --assign
```

Demo credentials (local only — not real accounts):

| Role       | Email                  | Password |
|------------|-------------------------|----------|
| Admin      | admin@example.com       | password |
| Technician | tech.north@example.com  | password |
| Technician | tech.central@example.com| password |
| Technician | tech.south@example.com  | password |

See [DEMO_SCRIPT.md](DEMO_SCRIPT.md) for a full walkthrough of the dispatch → mobile execution → completion flow.

## Notes

This is a demo/portfolio build, not a production deployment:

- The bundled `docker-compose.yml` runs the Nuxt dev server and `artisan serve` — fine for a demo, not production-hardened.
- Database credentials in `docker-compose.dev.yml` are placeholders for local use only.
- The CMMS integration talks to an in-memory fake adapter, not a real system.
