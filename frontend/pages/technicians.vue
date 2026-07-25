<template>
  <PageShell>
    <section class="mb-6 rounded-xl border border-slate-200/80 bg-white px-5 py-4 shadow-sm">
      <p class="text-xs font-black uppercase tracking-[0.16em] text-blue-700">Technician operations</p>
      <h2 class="mt-1 text-xl font-black text-slate-950">Electrician availability and field status</h2>
      <p class="mt-1 max-w-4xl text-sm font-semibold leading-6 text-slate-500">
        Review available electricians, active field work, last GPS activity, and technician skills before dispatch decisions.
      </p>
    </section>

    <div v-if="dispatchStore.error" class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
      {{ dispatchStore.error }}
    </div>

    <section class="mb-5 grid gap-3 md:grid-cols-[1fr_220px_auto]">
      <input
        v-model.trim="search"
        class="rounded-lg border border-slate-200 bg-white px-3 py-3 text-sm font-semibold outline-none transition focus:border-blue-300 focus:ring-2 focus:ring-blue-100"
        placeholder="Search electricians, skills, current jobs..."
      />
      <select v-model="availability" class="rounded-lg border border-slate-200 bg-white px-3 py-3 text-sm font-semibold outline-none transition focus:border-blue-300 focus:ring-2 focus:ring-blue-100">
        <option value="">All electricians</option>
        <option value="available">Available</option>
        <option value="busy">Busy / assigned</option>
        <option value="unavailable">Unavailable</option>
      </select>
      <button
        type="button"
        class="rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-700 shadow-sm transition hover:border-blue-200 hover:bg-blue-50 disabled:cursor-not-allowed disabled:opacity-60"
        :disabled="dispatchStore.loading"
        @click="loadTechnicians"
      >
        {{ dispatchStore.loading ? 'Loading...' : 'Refresh' }}
      </button>
    </section>

    <section class="grid gap-5 xl:grid-cols-[0.9fr_1.1fr]">
      <div>
        <div class="mb-3 flex items-center justify-between">
          <h3 class="text-sm font-black uppercase tracking-[0.14em] text-slate-500">Electricians</h3>
          <span class="text-xs font-bold text-slate-400">{{ filteredTechnicians.length }} shown</span>
        </div>

        <div v-if="dispatchStore.loading && dispatchStore.technicianStatus.length === 0" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-12 text-center text-sm font-bold text-slate-500">
          Loading electricians...
        </div>

        <div v-else-if="filteredTechnicians.length === 0" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-12 text-center text-sm font-bold text-slate-500">
          No electricians match this view.
        </div>

        <div v-else class="grid gap-3 md:grid-cols-2 xl:grid-cols-1">
          <button
            v-for="tech in filteredTechnicians"
            :key="tech.id"
            type="button"
            class="rounded-xl border p-4 text-left shadow-sm transition"
            :class="selectedTechnician?.id === tech.id ? 'border-blue-400 bg-blue-50 ring-2 ring-blue-300/40' : technicianCardClass(tech.availability)"
            @click="selectedTechnician = tech"
          >
            <div class="flex items-start justify-between gap-3">
              <div class="min-w-0">
                <h3 class="truncate text-base font-black text-slate-950">{{ tech.name }}</h3>
                <p class="mt-1 text-xs font-semibold text-slate-500">{{ tech.employee_code || tech.email }}</p>
              </div>
              <StatusBadge :label="tech.human_status_label || tech.availability" />
            </div>
            <p v-if="tech.current_job" class="mt-3 truncate text-sm font-bold text-slate-700">{{ tech.current_job.title }}</p>
            <p v-else class="mt-3 text-sm font-bold text-emerald-700">Ready for dispatch</p>
            <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs font-bold text-slate-500">
              <span v-if="tech.last_location">GPS {{ formatDateTime(tech.last_location.captured_at) }}</span>
              <span v-if="tech.skills?.length">{{ tech.skills.slice(0, 2).join(', ') }}</span>
            </div>
          </button>
        </div>
      </div>

      <div>
        <div v-if="!selectedTechnician" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-16 text-center">
          <p class="text-sm font-bold text-slate-700">Select an electrician to view operational detail.</p>
          <p class="mt-1 text-xs font-semibold text-slate-400">Availability, current job, GPS, and recent activity appear here.</p>
        </div>

        <div v-else class="space-y-5">
          <SectionCard title="Technician Detail" subtitle="Availability and dispatch context">
            <div class="flex flex-wrap items-start justify-between gap-4">
              <div>
                <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-400">{{ selectedTechnician.employee_code || 'Technician' }}</p>
                <h3 class="mt-1 text-xl font-black text-slate-950">{{ selectedTechnician.name }}</h3>
                <p class="mt-1 text-sm font-semibold text-slate-500">{{ selectedTechnician.email }}</p>
              </div>
              <StatusBadge :label="selectedTechnician.human_status_label || selectedTechnician.availability" />
            </div>

            <div class="mt-5 grid gap-3 md:grid-cols-2">
              <div v-for="item in selectedTechnicianFacts" :key="item.label" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                <p class="text-[11px] font-black uppercase tracking-[0.12em] text-slate-400">{{ item.label }}</p>
                <p class="mt-1 text-sm font-bold text-slate-800">{{ item.value }}</p>
              </div>
            </div>
          </SectionCard>

          <SectionCard title="Current Field Work" subtitle="Active assignment and job lifecycle">
            <div v-if="selectedTechnician.current_job" class="rounded-xl border border-blue-200 bg-blue-50/70 p-4">
              <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                  <p class="text-xs font-black text-blue-700">{{ selectedTechnician.current_job.external_id ? `CMMS ${selectedTechnician.current_job.external_id}` : 'Active job' }}</p>
                  <h4 class="mt-1 text-base font-black text-slate-950">{{ selectedTechnician.current_job.title }}</h4>
                </div>
                <StatusBadge :label="selectedTechnician.current_job.status" />
              </div>
              <div class="mt-3 flex flex-wrap gap-x-5 gap-y-1 text-xs font-bold text-slate-600">
                <span>Priority: {{ label(selectedTechnician.current_job.priority) }}</span>
                <span v-if="selectedTechnician.current_job.outage_type">Type: {{ label(selectedTechnician.current_job.outage_type) }}</span>
                <span v-if="selectedTechnician.current_assignment">Assigned: {{ formatDateTime(selectedTechnician.current_assignment.assigned_at) }}</span>
              </div>
            </div>
            <div v-else class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-8 text-center">
              <p class="text-sm font-black text-emerald-800">Available for dispatch</p>
              <p class="mt-1 text-xs font-semibold text-emerald-700/70">No active assignment is currently attached to this electrician.</p>
            </div>
          </SectionCard>

          <SectionCard title="Skills And Recent Activity" subtitle="Useful context for dispatcher decisions">
            <div class="space-y-4">
              <div>
                <p class="mb-2 text-xs font-black uppercase tracking-[0.14em] text-slate-400">Skills</p>
                <div v-if="selectedTechnician.skills?.length" class="flex flex-wrap gap-2">
                  <span v-for="skill in selectedTechnician.skills" :key="skill" class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-black text-slate-700">
                    {{ skill }}
                  </span>
                </div>
                <p v-else class="text-sm font-semibold text-slate-500">No skills recorded.</p>
              </div>

              <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-400">Last completed job</p>
                <p class="mt-1 text-sm font-black text-slate-900">{{ selectedTechnician.last_completed_job?.title || 'No completed job recorded' }}</p>
                <p class="mt-1 text-xs font-semibold text-slate-500">{{ formatDateTime(selectedTechnician.last_completed_job?.completed_at || null) }}</p>
              </div>
            </div>
          </SectionCard>
        </div>
      </div>
    </section>
  </PageShell>
</template>

<script setup lang="ts">
import type { TechnicianStatus } from '~/types/dispatch'

const route = useRoute()
const router = useRouter()
const dispatchStore = useDispatchStore()

const search = ref(String(route.query.search ?? ''))
const availability = ref(String(route.query.availability ?? ''))
const selectedTechnician = ref<TechnicianStatus | null>(null)

const filteredTechnicians = computed(() => {
  const q = search.value.toLowerCase()
  return dispatchStore.technicianStatus.filter((tech) => {
    const busy = tech.availability !== 'available' && tech.availability !== 'unavailable'
    const matchesAvailability =
      !availability.value ||
      tech.availability === availability.value ||
      (availability.value === 'busy' && busy)
    const text = [
      tech.name,
      tech.email,
      tech.employee_code,
      tech.phone,
      tech.current_job?.title,
      tech.current_job?.external_id,
      ...(tech.skills ?? []),
    ].filter(Boolean).join(' ').toLowerCase()
    return matchesAvailability && (!q || text.includes(q))
  })
})

const selectedTechnicianFacts = computed(() => {
  if (!selectedTechnician.value) return []
  return [
    { label: 'Availability', value: label(selectedTechnician.value.availability) },
    { label: 'Phone', value: selectedTechnician.value.phone || '—' },
    { label: 'Last GPS', value: gpsLabel(selectedTechnician.value) },
    { label: 'Last activity', value: formatDateTime(selectedTechnician.value.last_activity_at) },
  ]
})

watch([availability, search], () => {
  router.replace({
    query: {
      ...(availability.value ? { availability: availability.value } : {}),
      ...(search.value ? { search: search.value } : {}),
    },
  })
})

onMounted(async () => {
  await loadTechnicians()
  selectedTechnician.value = filteredTechnicians.value[0] ?? dispatchStore.technicianStatus[0] ?? null
})

async function loadTechnicians() {
  await Promise.all([
    dispatchStore.fetchTechnicianStatus(),
    dispatchStore.fetchAvailability(),
  ])
}

function technicianCardClass(availability: string) {
  if (availability === 'available') return 'border-emerald-200 bg-emerald-50/50 hover:border-emerald-300'
  if (availability === 'unavailable') return 'border-slate-200 bg-slate-50 opacity-70'
  return 'border-blue-200 bg-blue-50/40 hover:border-blue-300'
}

function gpsLabel(tech: TechnicianStatus) {
  if (!tech.last_location) return 'No GPS submitted'
  return `${Number(tech.last_location.latitude).toFixed(4)}, ${Number(tech.last_location.longitude).toFixed(4)}`
}

function formatDateTime(value: string | null | undefined) {
  if (!value) return '—'
  return new Intl.DateTimeFormat('en', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(value))
}

function label(value: string) {
  if (!value) return '—'
  return value.replaceAll('_', ' ').replace(/\b\w/g, (c) => c.toUpperCase())
}
</script>
