# Demo Checklist

Use this before the stakeholder demo.

| Check | PASS/FAIL | Notes |
|---|---|---|
| `docker compose ps` shows backend, frontend, mysql up |  |  |
| `php artisan demo:reset` succeeds |  |  |
| Admin login succeeds |  |  |
| Integrations CMMS health is healthy |  |  |
| Sync Now imports or updates 3 CMMS work orders |  |  |
| Dispatch Board loads |  |  |
| Imported work orders are visible |  |  |
| Recommendations load for selected work order |  |  |
| Assign technician succeeds |  |  |
| Duplicate assignment returns 422 |  |  |
| Active Assignments section shows assignment |  |  |
| Expo app starts |  |  |
| Technician login succeeds |  |  |
| My Jobs shows assigned job |  |  |
| Job Detail loads work order and asset |  |  |
| Accept succeeds |  |  |
| On The Way succeeds |  |  |
| Arrived succeeds |  |  |
| In Progress succeeds |  |  |
| GPS submit succeeds |  |  |
| Evidence upload succeeds |  |  |
| Complete succeeds |  |  |
| Admin timeline shows mobile events |  |  |
| Admin timeline shows `evidence_uploaded` |  |  |
| CMMS logs show `push_status` and `push_completion` |  |  |
| `npm run build` succeeds in frontend |  |  |
| `php artisan test` succeeds in backend |  |  |
| `npx expo-doctor` succeeds in mobile |  |  |
