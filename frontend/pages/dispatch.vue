<template>
  <PageShell>
    <section class="mb-6 rounded-xl border border-slate-200/80 bg-white px-5 py-4 shadow-sm">
      <p class="text-xs font-black uppercase tracking-[0.16em] text-blue-700">Active dispatch operations</p>
      <h2 class="mt-1 text-xl font-black text-slate-950">Field execution monitor</h2>
      <p class="mt-1 max-w-4xl text-sm font-semibold leading-6 text-slate-500">
        Monitor active assignments and technician lifecycle status. Create new assignments from the Work Orders page.
      </p>
    </section>

    <div v-if="dispatchStore.error" class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
      {{ dispatchStore.error }}
    </div>

    <section class="mb-6 grid gap-4 md:grid-cols-4">
      <div v-for="card in cards" :key="card.title" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-500">{{ card.title }}</p>
        <p class="mt-2 text-3xl font-black text-slate-950">{{ card.value }}</p>
        <p class="mt-1 text-xs font-semibold text-slate-400">{{ card.caption }}</p>
      </div>
    </section>

    <section class="grid gap-5 xl:grid-cols-[1.1fr_0.9fr]">
      <SectionCard title="Active Assignments" subtitle="Jobs currently assigned to electricians in the field">
        <div class="mb-4 flex items-center justify-between gap-3">
          <p class="text-sm font-semibold text-slate-500">Assignment lifecycle is updated by mobile job actions.</p>
          <button
            type="button"
            class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-black text-slate-700 shadow-sm transition hover:border-blue-200 hover:bg-blue-50 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="dispatchStore.loading"
            @click="loadDispatch"
          >
            {{ dispatchStore.loading ? 'Loading...' : 'Refresh' }}
          </button>
        </div>

        <div v-if="dispatchStore.loading && activeAssignments.length === 0" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-10 text-center text-sm font-bold text-slate-500">
          Loading active assignments...
        </div>

        <div v-else-if="activeAssignments.length === 0" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-10 text-center">
          <p class="text-sm font-bold text-slate-700">No active assignments right now.</p>
          <p class="mt-1 text-xs font-semibold text-slate-400">
            Open
            <NuxtLink to="/work-orders?status=pending_dispatch" class="text-blue-600 underline">Work Orders</NuxtLink>
            to assign a pending CMMS job.
          </p>
        </div>

        <div v-else class="space-y-3">
          <article
            v-for="assignment in activeAssignments"
            :key="assignment.id"
            class="rounded-xl border border-slate-200/80 bg-white p-4 shadow-sm"
          >
            <div class="flex flex-wrap items-start justify-between gap-3">
              <div class="min-w-0">
                <p class="text-xs font-black text-slate-400">
                  {{ assignment.work_order?.external_id ? `CMMS ${assignment.work_order.external_id}` : `Assignment #${assignment.id}` }}
                </p>
                <h3 class="mt-1 truncate text-base font-black text-slate-950">{{ assignment.work_order?.title ?? 'Job unavailable' }}</h3>
                <p class="mt-1 text-sm font-semibold text-slate-500">Electrician: {{ assignmentTechnicianName(assignment) }}</p>
              </div>
              <StatusBadge :label="assignment.status" />
            </div>

            <div class="mt-4">
              <LifecycleBar :status="assignment.status" />
            </div>

            <div class="mt-4 grid gap-2 text-xs font-bold text-slate-500 md:grid-cols-2">
              <span>Assigned: {{ formatDateTime(assignment.assigned_at) }}</span>
              <span v-if="assignment.accepted_at">Accepted: {{ formatDateTime(assignment.accepted_at) }}</span>
              <span v-if="assignment.arrived_at">Arrived: {{ formatDateTime(assignment.arrived_at) }}</span>
              <span v-if="assignment.started_at">Started: {{ formatDateTime(assignment.started_at) }}</span>
            </div>

            <p v-if="assignment.notes" class="mt-3 rounded-lg bg-slate-50 px-3 py-2 text-xs font-semibold leading-5 text-slate-600">
              {{ assignment.notes }}
            </p>

            <details class="mt-4">
              <summary class="cursor-pointer select-none text-xs font-bold text-slate-400 transition hover:text-slate-600">
                Cancel this assignment
              </summary>
              <div class="mt-2 flex gap-2">
                <input
                  v-model.trim="cancelNotes[assignment.id]"
                  type="text"
                  class="min-w-0 flex-1 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 outline-none transition focus:border-blue-300 focus:ring-2 focus:ring-blue-100"
                  placeholder="Reason for cancellation"
                />
                <button
                  type="button"
                  class="shrink-0 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-black text-red-700 transition hover:bg-red-100 disabled:cursor-not-allowed disabled:opacity-60"
                  :disabled="dispatchStore.loading"
                  @click="cancelAssignment(assignment.id)"
                >
                  {{ dispatchStore.loading ? '...' : 'Cancel' }}
                </button>
              </div>
            </details>
          </article>
        </div>
      </SectionCard>

      <SectionCard title="Jobs In Field" subtitle="Technician operational state from mobile workflow">
        <div v-if="fieldTechnicians.length === 0" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-10 text-center">
          <p class="text-sm font-bold text-slate-700">No electricians are currently in field execution.</p>
          <p class="mt-1 text-xs font-semibold text-slate-400">Assigned and accepted jobs will appear here.</p>
        </div>

        <div v-else class="space-y-3">
          <article
            v-for="tech in fieldTechnicians"
            :key="tech.id"
            class="rounded-xl border border-blue-200/70 bg-blue-50/40 p-4"
          >
            <div class="flex flex-wrap items-start justify-between gap-3">
              <div>
                <h3 class="text-base font-black text-slate-950">{{ tech.name }}</h3>
                <p class="mt-1 text-xs font-semibold text-slate-500">{{ tech.employee_code || tech.email }}</p>
              </div>
              <StatusBadge :label="tech.human_status_label || tech.availability" />
            </div>
            <p v-if="tech.current_job" class="mt-3 text-sm font-black text-slate-800">{{ tech.current_job.title }}</p>
            <div v-if="tech.current_job" class="mt-2 flex flex-wrap gap-x-5 gap-y-1 text-xs font-bold text-slate-500">
              <span v-if="tech.current_job.external_id">CMMS {{ tech.current_job.external_id }}</span>
              <span>Priority: {{ label(tech.current_job.priority) }}</span>
              <span v-if="tech.last_location">GPS: {{ formatDateTime(tech.last_location.captured_at) }}</span>
            </div>
            <div class="mt-4">
              <LifecycleBar :status="tech.availability" />
            </div>
          </article>
        </div>
      </SectionCard>
    </section>
  </PageShell>
</template>

<script setup lang="ts">
import type { DispatchAssignment } from '~/types/dispatch'

const dispatchStore = useDispatchStore()
const cancelNotes = reactive<Record<number, string>>({})

const activeStatuses = new Set(['created', 'sent_to_technician', 'accepted', 'on_the_way', 'arrived', 'in_progress'])

const activeAssignments = computed(() => dispatchStore.assignments.filter((assignment) => activeStatuses.has(assignment.status)))
const fieldTechnicians = computed(() => dispatchStore.technicianStatus.filter((tech) => tech.availability !== 'available' && tech.availability !== 'unavailable'))
const acceptedCount = computed(() => activeAssignments.value.filter((assignment) => assignment.status === 'accepted').length)
const inProgressCount = computed(() => activeAssignments.value.filter((assignment) => assignment.status === 'in_progress').length)

const cards = computed(() => [
  { title: 'Active Work', value: activeAssignments.value.length, caption: 'Assignments in field lifecycle' },
  { title: 'Accepted', value: acceptedCount.value, caption: 'Acknowledged by technicians' },
  { title: 'In Progress', value: inProgressCount.value, caption: 'Currently being executed' },
  { title: 'Field Technicians', value: fieldTechnicians.value.length, caption: 'Busy or moving on site' },
])

onMounted(loadDispatch)

async function loadDispatch() {
  await Promise.all([
    dispatchStore.fetchAssignments(),
    dispatchStore.fetchTechnicianStatus(),
  ])
}

async function cancelAssignment(assignmentId: number) {
  if (dispatchStore.loading) return

  const assignment = await dispatchStore.cancel(assignmentId, cancelNotes[assignmentId] || undefined)
  if (!assignment) return

  cancelNotes[assignmentId] = ''
  await loadDispatch()
}

function assignmentTechnicianName(assignment: DispatchAssignment) {
  return assignment.technician?.name ?? 'Unassigned'
}

function formatDateTime(value: string | null) {
  if (!value) return '—'
  return new Intl.DateTimeFormat('en', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(value))
}

function label(value: string) {
  if (!value) return '—'
  return value.replaceAll('_', ' ').replace(/\b\w/g, (c) => c.toUpperCase())
}
</script>
