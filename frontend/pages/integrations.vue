<template>
  <PageShell>
    <!-- Helper banner -->
    <div class="mb-5 rounded-xl border border-amber-200/70 bg-amber-50/60 px-5 py-4">
      <p class="text-sm font-bold text-amber-900">
        For this demo, Fake CMMS simulates the customer CMMS API. Use <strong>Sync Now</strong> to import sample work orders into the dispatch queue.
      </p>
    </div>

    <SectionCard class="mb-5" title="CMMS Connection" subtitle="Health status and sync controls for the CMMS adapter">
      <div v-if="cmmsError" class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
        {{ cmmsError }}
      </div>

      <div class="grid gap-4 lg:grid-cols-[0.8fr_1.2fr]">
        <div class="rounded-lg border border-slate-200/80 bg-white p-4 shadow-sm">
          <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
              <p class="text-xs font-black uppercase tracking-wider text-slate-500">CMMS Health</p>
              <h2 class="mt-1 text-xl font-black text-slate-950">{{ cmmsHealth?.system ?? 'fake-cmms' }}</h2>
              <p class="mt-1 text-sm font-semibold text-slate-500">{{ cmmsHealth?.mode ?? 'demo-only' }}</p>
            </div>
            <span :class="cmmsHealth?.status === 'online' ? badgeClass('online') : badgeClass('offline')">
              {{ cmmsHealth?.status ?? 'unknown' }}
            </span>
          </div>

          <div class="mt-4 grid gap-2 text-xs font-bold text-slate-500">
            <span>Auth: {{ cmmsHealth?.authenticated ? 'API key' : 'Demo public fake API' }}</span>
            <span>Checked: {{ formatDateTime(cmmsHealth?.checked_at ?? null) }}</span>
            <span>Last import: {{ lastImportLabel }}</span>
          </div>

          <div class="mt-4 space-y-2">
            <button
              type="button"
              class="inline-flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 px-4 py-3 text-sm font-black text-white shadow-lg shadow-blue-900/20 transition hover:from-blue-500 hover:to-cyan-500 disabled:cursor-not-allowed disabled:opacity-60"
              :disabled="cmmsLoading"
              @click="syncNow"
            >
              {{ cmmsLoading ? 'Syncing...' : '⬇ Sync Now — Import jobs from CMMS' }}
            </button>
            <button
              type="button"
              class="inline-flex w-full items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-500 shadow-sm transition hover:border-blue-200 hover:bg-blue-50 disabled:cursor-not-allowed disabled:opacity-60"
              :disabled="cmmsLoading"
              @click="loadCmms"
            >
              {{ cmmsLoading ? 'Checking...' : 'Check Health' }}
            </button>
          </div>
        </div>

        <div class="rounded-lg border border-slate-200/80 bg-white p-4 shadow-sm">
          <div class="mb-3 flex items-center justify-between gap-3">
            <div>
              <p class="text-xs font-black uppercase tracking-wider text-slate-500">Sync Logs</p>
              <h3 class="mt-1 text-lg font-black text-slate-950">Latest CMMS activity</h3>
            </div>
            <span :class="badgeClass(String(cmmsLogs.length))">{{ cmmsLogs.length }} total</span>
          </div>

          <div v-if="cmmsLogs.length === 0" class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm font-bold text-slate-500">
            No CMMS sync logs yet.
          </div>

          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200/80 text-sm">
              <thead class="bg-gradient-to-r from-slate-50 to-blue-50/60">
                <tr>
                  <th class="whitespace-nowrap px-3 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500">Time</th>
                  <th class="whitespace-nowrap px-3 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500">Direction</th>
                  <th class="whitespace-nowrap px-3 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500">Action</th>
                  <th class="whitespace-nowrap px-3 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500">External Ref</th>
                  <th class="whitespace-nowrap px-3 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 bg-white">
                <tr v-for="log in cmmsLogs.slice(0, 5)" :key="log.id" class="transition hover:bg-blue-50/50">
                  <td class="whitespace-nowrap px-3 py-3 font-semibold text-slate-600">{{ formatDateTime(log.created_at) }}</td>
                  <td class="whitespace-nowrap px-3 py-3 font-semibold text-slate-700">{{ labelize(log.direction) }}</td>
                  <td class="whitespace-nowrap px-3 py-3 font-semibold text-slate-700">{{ labelize(log.action) }}</td>
                  <td class="whitespace-nowrap px-3 py-3 font-semibold text-slate-700">{{ log.external_id ?? '—' }}</td>
                  <td class="whitespace-nowrap px-3 py-3"><span :class="badgeClass(log.status)">{{ labelize(log.status) }}</span></td>
                </tr>
              </tbody>
            </table>
            <p v-if="cmmsLogs.length > 5" class="mt-2 text-center text-xs font-semibold text-slate-400">
              Showing latest 5 of {{ cmmsLogs.length }} entries
            </p>
          </div>
        </div>
      </div>
    </SectionCard>

    <section class="grid gap-5 xl:grid-cols-[1.1fr_0.9fr]">
      <SectionCard title="Enterprise Integrations" subtitle="CMMS, GIS, OLCM, barcode master, and BI connections">
        <DataTable :columns="columns" :rows="integrationMatrix" />
      </SectionCard>

      <SectionCard title="Integration Health">
        <div class="space-y-4">
          <div v-for="item in integrationMatrix" :key="item.system" class="rounded-lg border border-slate-200/70 bg-white p-4 shadow-sm ring-1 ring-white/70">
            <div class="flex items-center justify-between gap-3">
              <div>
                <div class="font-black text-slate-950">{{ item.system }}</div>
                <div class="text-sm text-slate-500">{{ t(item.direction) }} / {{ t(item.protocol) }}</div>
              </div>
              <StatusBadge :label="item.status" />
            </div>
          </div>
        </div>
      </SectionCard>
    </section>

  </PageShell>
</template>

<script setup lang="ts">
import { useLanguage } from '~/composables/useLanguage'
import { integrationMatrix } from '~/data/platform'
import { createCmmsService } from '~/services/cmms.service'
import { ApiError } from '~/types/api'
import type { CmmsHealth, CmmsSyncLog, CmmsSyncSummary } from '~/types/cmms'

const { t } = useLanguage()
const authStore = useAuthStore()
const config = useRuntimeConfig()

const cmmsHealth = ref<CmmsHealth | null>(null)
const cmmsLogs = ref<CmmsSyncLog[]>([])
const lastImport = ref<CmmsSyncSummary | null>(null)
const cmmsLoading = ref(false)
const cmmsError = ref<string | null>(null)

const lastImportLabel = computed(() => {
  if (!lastImport.value) return 'No sync run in this session'
  return `${lastImport.value.fetched} fetched, ${lastImport.value.created} created, ${lastImport.value.updated} updated`
})

onMounted(async () => {
  await loadCmms()
})

function cmmsService() {
  return createCmmsService(config.public.apiBase as string, authStore.token)
}

async function loadCmms() {
  cmmsLoading.value = true
  cmmsError.value = null
  try {
    const [health, logs] = await Promise.all([
      cmmsService().health(),
      cmmsService().logs(),
    ])
    cmmsHealth.value = health.data
    cmmsLogs.value = logs.data.data
  }
  catch (err) {
    cmmsError.value = err instanceof ApiError ? err.message : 'Unable to load CMMS status'
  }
  finally {
    cmmsLoading.value = false
  }
}

async function syncNow() {
  cmmsLoading.value = true
  cmmsError.value = null
  try {
    const result = await cmmsService().syncNow()
    lastImport.value = result.data
    await loadCmms()
  }
  catch (err) {
    cmmsError.value = err instanceof ApiError ? err.message : 'Unable to sync CMMS'
  }
  finally {
    cmmsLoading.value = false
  }
}

function formatDateTime(value: string | null) {
  if (!value) return '-'
  return new Intl.DateTimeFormat('en', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(value))
}

function labelize(value: string) {
  return value.replaceAll('_', ' ').replace(/\b\w/g, (letter) => letter.toUpperCase())
}

function badgeClass(value: string) {
  const normalized = value.toLowerCase()
  const base = 'inline-flex max-w-full items-center whitespace-nowrap rounded-full border px-3 py-1 text-xs font-black leading-4 shadow-sm'

  if (['online', 'success'].includes(normalized)) return `${base} border-emerald-200 bg-emerald-50 text-emerald-700`
  if (['failed', 'offline'].includes(normalized)) return `${base} border-red-200 bg-red-50 text-red-700`
  return `${base} border-blue-200 bg-blue-50 text-blue-700`
}

const columns = [
  { key: 'system', label: 'System' },
  { key: 'direction', label: 'Direction' },
  { key: 'protocol', label: 'Protocol' },
  { key: 'frequency', label: 'Frequency' },
  { key: 'status', label: 'Status', badge: true }
]

</script>
