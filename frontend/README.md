# Enterprise Management Platform Admin Panel

Nuxt 3 admin panel for the Digital Field Work Management / Smart Inspection proposal.

## Admin Modules

- Dashboard with operational KPIs, work order status, asset health, and integration status
- Work order administration for assignment, scheduling, filtering, and CMMS execution tracking
- Smart inspection form templates and inspection record review
- Asset master registry with barcode, GIS, defect, and condition index fields
- Integration registry for CMMS, GIS, OLCM, barcode master, and BI endpoints
- User, role, and platform setting screens for later customization

Most editable sample data is centralized in `data/platform.ts`.

## Run

```bash
npm install
npm run dev
```

Open:

```text
http://localhost:3000
```

## Build

```bash
npm run build
npm run preview
```

## Pages

- `/` Dashboard
- `/work-orders`
- `/inspections`
- `/assets`
- `/integrations`
- `/users`
- `/settings`
