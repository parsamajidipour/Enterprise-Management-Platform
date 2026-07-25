# Enterprise Management Platform Demo Script

## Demo Credentials

| Role | Email | Password |
|---|---|---|
| Admin | `admin@example.com` | `password` |
| Technician | `tech.north@example.com` | `password` |
| Technician | `tech.central@example.com` | `password` |
| Technician | `tech.south@example.com` | `password` |

## URLs

| App | URL |
|---|---|
| Admin frontend | http://localhost or your VPS domain |
| Backend API | /api on the same domain |
| Adminer | http://127.0.0.1:8090 through SSH tunnel |
| Expo mobile | Run from `mobile/` with `npm start` |

For a real phone on the same network, start Expo with a LAN API URL:

```bash
cd mobile
EXPO_PUBLIC_API_BASE=http://YOUR_DOMAIN_OR_VPS_IP/api npm start
```

## Reset Command

```bash
docker compose exec backend php artisan demo:reset
```

For a pre-assigned mobile job:

```bash
docker compose exec backend php artisan demo:reset --assign
```

## Demo Steps And Talk Track

1. Start clean.
   - Command: `docker compose exec backend php artisan demo:reset`
   - Say: "We can reset the demo to a known state at any time. This imports fake CMMS work orders through the adapter layer."

2. Login as admin.
   - Open http://localhost or your VPS domain.
   - Use `admin@example.com / password`
   - Say: "The dispatcher works from the Nuxt admin panel."

3. Open Integrations.
   - Check CMMS health.
   - Click Sync Now.
   - Say: "The CMMS is simulated, but it uses the same adapter contract planned for the real customer CMMS."

4. Open Dispatch Board.
   - Select an imported CMMS work order.
   - Show recommendations.
   - Say: "The backend ranks technicians by availability, distance, and active workload."

5. Assign a technician.
   - Click Assign on the recommended technician.
   - Show Active Assignments.
   - Say: "The work order now has exactly one active assignment. Duplicate assignment is blocked."

6. Open Expo mobile app.
   - Login as the assigned technician.
   - Say: "The electrician sees only jobs assigned to them."

7. Open job detail.
   - Show work order, asset, location, CMMS external ID, and timeline summary.
   - Say: "The mobile view is focused on field execution, not dispatch management."

8. Execute lifecycle.
   - Accept Job.
   - Set On The Way.
   - Set Arrived.
   - Set In Progress.
   - Say: "Each state transition is validated and written to the work order timeline."

9. Submit GPS.
   - Tap Send GPS.
   - Say: "GPS can be captured from the technician app and stored for dispatch visibility."

10. Upload evidence.
   - Upload a photo.
   - Say: "Evidence is attached to the work order through the existing inspection evidence flow."

11. Complete job.
   - Enter notes and condition score.
   - Tap Complete Job.
   - Say: "Completion updates assignment and work order status, then pushes completion back to CMMS."

12. Return to admin Dispatch Board.
   - Reopen the selected work order timeline.
   - Show mobile events, evidence upload, and CMMS pushback.
   - Say: "The dispatcher sees the complete lifecycle from CMMS import to field completion."

## Fallback Commands

Check services:

```bash
docker compose ps
docker compose logs -f frontend
docker compose logs -f backend
```

Reset demo data:

```bash
docker compose exec backend php artisan demo:reset --assign
```

Run backend checks:

```bash
docker compose exec backend php artisan route:list --path=api
docker compose exec backend php artisan test
```

Run frontend build:

```bash
docker compose exec frontend npm run build
```

Run mobile checks:

```bash
cd mobile
npm run typecheck
npx expo-doctor
```
