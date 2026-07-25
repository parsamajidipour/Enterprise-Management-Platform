<template>
  <PageShell>
    <section class="mb-6">
      <div class="rounded-xl border border-slate-200/80 bg-white px-5 py-4 shadow-sm">
        <p class="text-xs font-black uppercase tracking-[0.16em] text-blue-700">Operations overview</p>
        <h2 class="mt-1 text-xl font-black text-slate-950">Digital field work control summary</h2>
        <p class="mt-1 max-w-3xl text-sm font-semibold leading-6 text-slate-500">
          High-level CMMS, dispatch, and field execution status. Open a card to continue the demo workflow.
        </p>
      </div>
    </section>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
      <NuxtLink
        v-for="card in overviewCards"
        :key="card.title"
        :to="card.to"
        class="group rounded-xl border border-slate-200/80 bg-white p-5 shadow-sm ring-1 ring-white transition hover:border-blue-300 hover:bg-blue-50/30 hover:shadow-md"
      >
        <div class="flex items-start justify-between gap-4">
          <div>
            <p class="text-sm font-black text-slate-500">{{ card.title }}</p>
            <p class="mt-2 text-3xl font-black text-slate-950">{{ card.value }}</p>
          </div>
          <span :class="cardBadgeClass(card.tone)">{{ card.icon }}</span>
        </div>
        <div class="mt-5 flex items-center justify-between gap-3 text-sm">
          <span class="font-semibold text-slate-500">{{ card.caption }}</span>
          <span class="font-black text-blue-600 transition group-hover:translate-x-0.5">Open</span>
        </div>
      </NuxtLink>
    </section>

    <section class="mt-6 grid gap-5 xl:grid-cols-[1.1fr_0.9fr]">
      <SectionCard title="Demo Flow" subtitle="Recommended stakeholder presentation path">
        <div class="grid gap-3 md:grid-cols-4">
          <NuxtLink
            v-for="step in demoSteps"
            :key="step.title"
            :to="step.to"
            class="rounded-xl border border-slate-200 bg-slate-50/70 p-4 transition hover:border-blue-200 hover:bg-blue-50"
          >
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 text-sm font-black text-white">{{ step.number }}</span>
            <h3 class="mt-3 text-sm font-black text-slate-950">{{ step.title }}</h3>
            <p class="mt-1 text-xs font-semibold leading-5 text-slate-500">{{ step.description }}</p>
          </NuxtLink>
        </div>
      </SectionCard>

      <SectionCard title="System Readiness" subtitle="Current demo services and integration status">
        <div class="space-y-3">
          <div class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 bg-white px-4 py-3">
            <div>
              <p class="text-sm font-black text-slate-950">CMMS Adapter</p>
              <p class="text-xs font-semibold text-slate-500">{{ cmmsMessage }}</p>
            </div>
            <StatusBadge :label="cmmsStatusLabel" />
          </div>
          <div class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 bg-white px-4 py-3">
            <div>
              <p class="text-sm font-black text-slate-950">Dispatch Queue</p>
              <p class="text-xs font-semibold text-slate-500">Assignments and technician availability are API-driven.</p>
            </div>
            <StatusBadge label="Ready" />
          </div>
          <NuxtLink
            to="/integrations"
            class="flex items-center justify-between rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm font-black text-blue-800 transition hover:bg-blue-100"
          >
            Open Integrations
            <span>→</span>
          </NuxtLink>
        </div>
      </SectionCard>
    </section>
  </PageShell>
</template>

<script setup lang="ts">
import { createCmmsService } from '~/services/cmms.service'
import { createHttp } from '~/services/http'
import { type PaginatedData } from '~/types/api'
import type { WorkOrderSummary } from '~/types/dispatch'

const config = useRuntimeConfig()
const authStore = useAuthStore()
const dispatchStore = useDispatchStore()

const workOrders = ref<WorkOrderSummary[]>([])
const cmmsStatusLabel = ref('Checking')
const cmmsMessage = ref('Checking fake CMMS health...')

const activeStatuses = new Set(['created', 'sent_to_technician', 'accepted', 'on_the_way', 'arrived', 'in_progress'])
const pendingStatuses = new Set(['pending', 'pending_dispatch', 'imported'])

const pendingJobs = computed(() => workOrders.value.filter((wo) => pendingStatuses.has(wo.status) || !wo.assignee).length)
const activeAssignments = computed(() => dispatchStore.assignments.filter((assignment) => activeStatuses.has(assignment.status)).length)
const availableElectricians = computed(() => dispatchStore.availability.filter((item) => item.availability === 'available').length)
const busyElectricians = computed(() => dispatchStore.availability.filter((item) => item.availability !== 'available').length)
const completedToday = computed(() => {
  const today = new Date().toDateString()
  return workOrders.value.filter((wo) => wo.completed_at && new Date(wo.completed_at).toDateString() === today).length
})
const criticalHighJobs = computed(() => workOrders.value.filter((wo) => ['critical', 'high'].includes((wo.priority ?? '').toLowerCase())).length)

const overviewCards = computed(() => [
  { title: 'Pending Jobs', value: pendingJobs.value, caption: 'Waiting for dispatch', icon: pendingJobs.value, tone: 'blue', to: '/work-orders?status=pending_dispatch' },
  { title: 'Active Assignments', value: activeAssignments.value, caption: 'Currently in field execution', icon: activeAssignments.value, tone: 'orange', to: '/dispatch' },
  { title: 'Available Electricians', value: availableElectricians.value, caption: 'Ready for assignment', icon: availableElectricians.value, tone: 'green', to: '/technicians?availability=available' },
  { title: 'Busy Electricians', value: busyElectricians.value, caption: 'Working or unavailable', icon: busyElectricians.value, tone: 'orange', to: '/technicians?availability=busy' },
  { title: 'Jobs Completed Today', value: completedToday.value, caption: 'Closed by mobile execution', icon: completedToday.value, tone: 'green', to: '/work-orders?status=completed' },
  { title: 'Critical / High Jobs', value: criticalHighJobs.value, caption: 'Priority work queue', icon: criticalHighJobs.value, tone: 'red', to: '/work-orders?priority=critical_high' },
  { title: 'CMMS Sync Status', value: cmmsStatusLabel.value, caption: 'Adapter health and sync logs', icon: 'CMMS', tone: cmmsStatusLabel.value === 'Online' ? 'green' : 'orange', to: '/integrations' },
])

const demoSteps = [
  { number: '1', title: 'Sync CMMS', description: 'Import demo outage and work order records.', to: '/integrations' },
  { number: '2', title: 'Open Work Orders', description: 'Select a pending job and review recommendations.', to: '/work-orders?status=pending_dispatch' },
  { number: '3', title: 'Assign Electrician', description: 'Dispatch the best available technician.', to: '/work-orders?status=pending_dispatch' },
  { number: '4', title: 'Monitor Dispatch', description: 'Track lifecycle, active assignments, and field status.', to: '/dispatch' },
]

onMounted(async () => {
  await Promise.all([
    loadWorkOrders(),
    dispatchStore.fetchAvailability(),
    dispatchStore.fetchAssignments(),
    loadCmmsHealth(),
  ])
})

async function loadWorkOrders() {
  try {
    const http = createHttp(config.public.apiBase as string, authStore.token)
    const res = await http.get<PaginatedData<WorkOrderSummary>>('/work-orders', { query: { per_page: 100 } })
    workOrders.value = res.data.data
  }
  catch {
    workOrders.value = []
  }
}

async function loadCmmsHealth() {
  try {
    const res = await createCmmsService(config.public.apiBase as string, authStore.token).health()
    cmmsStatusLabel.value = res.data.status === 'online' ? 'Online' : 'Check'
    cmmsMessage.value = res.message || 'Fake CMMS adapter checked.'
  }
  catch {
    cmmsStatusLabel.value = 'Check'
    cmmsMessage.value = 'CMMS health could not be checked from dashboard.'
  }
}

function cardBadgeClass(tone: string) {
  const base = 'flex h-12 min-w-12 items-center justify-center rounded-lg border px-2 text-sm font-black shadow-sm'
  if (tone === 'green') return `${base} border-emerald-200 bg-emerald-50 text-emerald-700`
  if (tone === 'orange') return `${base} border-amber-200 bg-amber-50 text-amber-700`
  if (tone === 'red') return `${base} border-red-200 bg-red-50 text-red-700`
  return `${base} border-blue-200 bg-blue-50 text-blue-700`
}
</script>
