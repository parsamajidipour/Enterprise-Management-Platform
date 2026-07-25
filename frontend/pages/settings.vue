<template>
  <PageShell>
    <section class="grid gap-5 xl:grid-cols-[1fr_0.8fr]">
      <SectionCard title="Platform Settings" subtitle="Operational defaults administrators can adjust later">
        <DataTable :columns="settingColumns" :rows="settings" />
      </SectionCard>

      <SectionCard title="Security Controls" subtitle="Baseline controls for critical transmission infrastructure">
        <div class="space-y-3">
          <div v-for="control in controls" :key="control.title" class="rounded-lg border border-slate-200/70 bg-white p-4 shadow-sm ring-1 ring-white/70">
            <div class="flex items-center justify-between gap-3">
              <div>
                <div class="font-black text-slate-950">{{ t(control.title) }}</div>
                <p class="mt-1 text-sm text-slate-500">{{ t(control.text) }}</p>
              </div>
              <StatusBadge :label="control.status" />
            </div>
          </div>
        </div>
      </SectionCard>
    </section>
  </PageShell>
</template>

<script setup lang="ts">
import { tx, useLanguage } from '~/composables/useLanguage'
import { settings } from '~/data/platform'

const { t } = useLanguage()

const settingColumns = [
  { key: 'key', label: 'Setting' },
  { key: 'value', label: 'Value', badge: true },
  { key: 'owner', label: 'Owner' }
]

const controls = [
  { title: 'MFA and RBAC', text: 'Multi-factor authentication and role-based permissions for all modules.', status: tx('status.active', 'Active') },
  { title: 'Encryption', text: 'AES-256 at rest and TLS 1.3 for integration traffic.', status: tx('status.active', 'Active') },
  { title: 'Mobile Device Management', text: 'Remote wipe and device policy enforcement for field devices.', status: tx('status.active', 'Active') },
  { title: 'Audit Logs', text: 'Immutable logs for user actions, status changes, and data updates.', status: tx('status.active', 'Active') }
]
</script>
