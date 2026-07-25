<template>
  <PageShell>
    <section class="grid gap-5 xl:grid-cols-[1.15fr_0.85fr]">
      <SectionCard title="Users" subtitle="Provision field inspectors, supervisors, asset managers, and administrators">
        <DataTable :columns="userColumns" :rows="users" />
      </SectionCard>

      <SectionCard title="Roles & Access" subtitle="RBAC structure aligned with proposal security requirements">
        <DataTable :columns="roleColumns" :rows="roles" />
      </SectionCard>
    </section>
  </PageShell>
</template>

<script setup lang="ts">
import { roles, users } from '~/data/platform'

const userColumns = [
  { key: 'name', label: 'Name' },
  { key: 'email', label: 'Email' },
  { key: 'role', label: 'Role' },
  { key: 'team', label: 'Team' },
  { key: 'status', label: 'Status', badge: true }
]

const roleColumns = [
  { key: 'role', label: 'Role' },
  { key: 'users', label: 'Users' },
  { key: 'access', label: 'Access' }
]
</script>
