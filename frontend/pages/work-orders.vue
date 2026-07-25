<template>
  <PageShell>
    <section class="mb-6 rounded-xl border border-slate-200/80 bg-white px-5 py-4 shadow-sm">
      <p class="text-xs font-black uppercase tracking-[0.16em] text-blue-700">Work order control</p>
      <h2 class="mt-1 text-xl font-black text-slate-950">Manage CMMS jobs and dispatch decisions</h2>
      <p class="mt-1 max-w-4xl text-sm font-semibold leading-6 text-slate-500">
        Select a work order to review details, recommended electricians, and assign the job.
      </p>
    </section>

    <div v-if="pageError || dispatchStore.error" class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
      {{ pageError || dispatchStore.error }}
    </div>

    <section class="mb-5 grid gap-3 md:grid-cols-[1fr_180px_180px_auto]">
      <input
        v-model.trim="search"
        class="rounded-lg border border-slate-200 bg-white px-3 py-3 text-sm font-semibold outline-none transition focus:border-blue-300 focus:ring-2 focus:ring-blue-100"
        placeholder="Search work orders, CMMS refs, assets, locations..."
      />
      <select v-model="status" class="rounded-lg border border-slate-200 bg-white px-3 py-3 text-sm font-semibold outline-none transition focus:border-blue-300 focus:ring-2 focus:ring-blue-100">
        <option value="">All statuses</option>
        <option value="pending_dispatch">Pending dispatch</option>
        <option value="assigned">Assigned</option>
        <option value="accepted">Accepted</option>
        <option value="on_the_way">On the way</option>
        <option value="arrived">Arrived</option>
        <option value="in_progress">In progress</option>
        <option value="completed">Completed</option>
      </select>
      <select v-model="priority" class="rounded-lg border border-slate-200 bg-white px-3 py-3 text-sm font-semibold outline-none transition focus:border-blue-300 focus:ring-2 focus:ring-blue-100">
        <option value="">All priorities</option>
        <option value="critical_high">Critical / High</option>
        <option value="critical">Critical</option>
        <option value="high">High</option>
        <option value="medium">Medium</option>
        <option value="low">Low</option>
      </select>
      <button
        type="button"
        class="rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-700 shadow-sm transition hover:border-blue-200 hover:bg-blue-50 disabled:cursor-not-allowed disabled:opacity-60"
        :disabled="loading"
        @click="refreshPage"
      >
        {{ loading ? 'Loading...' : 'Refresh' }}
      </button>
    </section>

    <section class="grid gap-5 xl:grid-cols-[0.92fr_1.08fr]">
      <div>
        <div class="mb-3 flex items-center justify-between">
          <h3 class="text-sm font-black uppercase tracking-[0.14em] text-slate-500">Work queue</h3>
          <span class="text-xs font-bold text-slate-400">{{ filteredWorkOrders.length }} shown</span>
        </div>

        <div v-if="loading && workOrders.length === 0" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-12 text-center text-sm font-bold text-slate-500">
          Loading work orders...
        </div>

        <div v-else-if="filteredWorkOrders.length === 0" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-12 text-center">
          <p class="text-sm font-bold text-slate-700">No work orders match this view.</p>
          <p class="mt-1 text-xs font-semibold text-slate-400">Sync CMMS or adjust filters.</p>
        </div>

        <div v-else class="space-y-3">
          <button
            v-for="workOrder in filteredWorkOrders"
            :key="workOrder.id"
            type="button"
            class="block w-full rounded-xl border p-4 text-left shadow-sm transition"
            :class="selectedWorkOrder?.id === workOrder.id
              ? 'border-blue-400 bg-blue-50/90 ring-2 ring-blue-300/40'
              : 'border-slate-200/80 bg-white hover:border-blue-200 hover:bg-blue-50/30'"
            @click="selectWorkOrder(workOrder)"
          >
            <div class="flex flex-wrap items-start justify-between gap-3">
              <div class="min-w-0">
                <p class="text-xs font-black text-slate-400">
                  {{ workOrder.external_id ? `CMMS ${workOrder.external_id}` : `WO-${workOrder.id}` }}
                </p>
                <h3 class="mt-1 text-base font-black leading-6 text-slate-950">{{ workOrder.title }}</h3>
                <p class="mt-1 text-sm font-semibold text-slate-500">{{ assetLabel(workOrder) }}</p>
              </div>
              <div class="flex shrink-0 flex-wrap gap-2">
                <StatusBadge :label="workOrder.priority" />
                <StatusBadge :label="workOrder.status" />
              </div>
            </div>
            <div class="mt-3 flex flex-wrap gap-x-5 gap-y-1 text-xs font-bold text-slate-500">
              <span>{{ formatDate(workOrder.scheduled_at ?? workOrder.reported_at ?? null) }}</span>
              <span v-if="workOrder.outage_type">Type: {{ label(workOrder.outage_type) }}</span>
              <span v-if="workOrder.latitude != null">GPS: {{ coordinatesLabel(workOrder) }}</span>
            </div>
          </button>
        </div>
      </div>

      <div>
        <div v-if="!selectedWorkOrder" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-16 text-center">
          <p class="text-sm font-bold text-slate-700">Select a work order to open dispatch detail.</p>
          <p class="mt-1 text-xs font-semibold text-slate-400">Recommendations appear only inside the selected job.</p>
        </div>

        <div v-else class="space-y-5">
          <SectionCard title="Work Order Detail" subtitle="CMMS job information and dispatch context">
            <div class="flex flex-wrap items-start justify-between gap-4">
              <div class="min-w-0">
                <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-400">
                  {{ selectedWorkOrder.external_id ? `CMMS Ref ${selectedWorkOrder.external_id}` : `WO-${selectedWorkOrder.id}` }}
                </p>
                <h3 class="mt-1 text-xl font-black leading-7 text-slate-950">{{ selectedWorkOrder.title }}</h3>
                <p v-if="selectedWorkOrder.description" class="mt-2 text-sm font-semibold leading-6 text-slate-500">
                  {{ selectedWorkOrder.description }}
                </p>
              </div>
              <div class="flex flex-wrap gap-2">
                <StatusBadge :label="selectedWorkOrder.priority" />
                <StatusBadge :label="selectedWorkOrder.status" />
              </div>
            </div>

            <div class="mt-5 grid gap-3 md:grid-cols-2">
              <div v-for="item in selectedWorkOrderFacts" :key="item.label" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                <p class="text-[11px] font-black uppercase tracking-[0.12em] text-slate-400">{{ item.label }}</p>
                <p class="mt-1 text-sm font-bold text-slate-800">{{ item.value }}</p>
              </div>
            </div>
          </SectionCard>

          <SectionCard title="Recommended Electricians" subtitle="Ranked by availability, distance, and workload">
            <textarea
              v-model.trim="assignmentNotes"
              rows="2"
              class="mb-4 block w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-semibold text-slate-700 outline-none transition focus:border-blue-300 focus:ring-2 focus:ring-blue-100"
              placeholder="Optional notes for the electrician..."
            />

            <div v-if="activeAssignmentForSelected" class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-800">
              This work order already has an active assignment. Monitor it from Dispatch.
            </div>

            <div v-if="dispatchStore.loading && dispatchStore.recommendations.length === 0" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm font-bold text-slate-500">
              Loading recommendations...
            </div>
            <div v-else-if="dispatchStore.recommendations.length === 0" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm font-bold text-slate-500">
              No recommendations available for this work order.
            </div>
            <div v-else class="space-y-3">
              <article
                v-for="(recommendation, idx) in dispatchStore.recommendations"
                :key="recommendation.technician.id"
                class="rounded-xl border p-4 shadow-sm"
                :class="idx === 0 ? 'border-emerald-300 bg-emerald-50/60' : 'border-slate-200 bg-white'"
              >
                <div class="flex flex-wrap items-start justify-between gap-3">
                  <div>
                    <p class="text-xs font-black text-slate-400">{{ idx === 0 ? 'Best match' : `Option ${idx + 1}` }}</p>
                    <h4 class="mt-1 text-base font-black text-slate-950">{{ recommendation.technician.name }}</h4>
                    <p class="mt-1 text-xs font-semibold text-slate-500">{{ recommendation.reason }}</p>
                  </div>
                  <StatusBadge :label="recommendation.availability" />
                </div>
                <div class="mt-3 flex flex-wrap gap-x-5 gap-y-1 text-xs font-bold text-slate-500">
                  <span>{{ recommendation.distance_km == null ? 'Distance unavailable' : `${recommendation.distance_km} km away` }}</span>
                  <span>{{ recommendation.active_jobs_count }} active jobs</span>
                  <span>{{ matchLabel(recommendation.score) }}</span>
                </div>
                <button
                  type="button"
                  class="mt-4 inline-flex w-full items-center justify-center rounded-xl px-4 py-3 text-sm font-black shadow-sm transition disabled:cursor-not-allowed disabled:opacity-60"
                  :class="canAssign(recommendation)
                    ? 'bg-blue-700 text-white hover:bg-blue-600'
                    : 'border border-slate-200 bg-white text-slate-400'"
                  :disabled="!canAssign(recommendation) || dispatchStore.loading"
                  @click="assignRecommendation(recommendation)"
                >
                  {{ dispatchStore.loading ? 'Assigning...' : canAssign(recommendation) ? 'Assign to this electrician' : 'Not available for assignment' }}
                </button>
              </article>
            </div>
          </SectionCard>

        </div>
      </div>
    </section>
  </PageShell>
</template>

<script setup lang="ts">
import { createHttp } from '~/services/http'
import { ApiError, type PaginatedData } from '~/types/api'
import type { DispatchAssignment, DispatchRecommendation, WorkOrderSummary } from '~/types/dispatch'

const route = useRoute()
const router = useRouter()
const config = useRuntimeConfig()
const authStore = useAuthStore()
const dispatchStore = useDispatchStore()

const workOrders = ref<WorkOrderSummary[]>([])
const loading = ref(false)
const pageError = ref<string | null>(null)
const selectedWorkOrder = ref<WorkOrderSummary | null>(null)
const search = ref(String(route.query.search ?? ''))
const status = ref(String(route.query.status ?? ''))
const priority = ref(String(route.query.priority ?? ''))
const assignmentNotes = ref('')

const activeAssignmentStatuses = new Set(['created', 'sent_to_technician', 'accepted', 'on_the_way', 'arrived', 'in_progress'])

const filteredWorkOrders = computed(() => {
  const q = search.value.toLowerCase()
  return workOrders.value.filter((wo) => {
    const text = [
      wo.title,
      wo.description,
      wo.external_id,
      wo.external_reference,
      wo.asset?.code,
      wo.asset?.name,
      wo.asset?.location,
      wo.status,
      wo.priority,
    ].filter(Boolean).join(' ').toLowerCase()

    const matchesSearch = !q || text.includes(q)
    const matchesStatus = !status.value || wo.status === status.value
    const matchesPriority = !priority.value ||
      wo.priority === priority.value ||
      (priority.value === 'critical_high' && ['critical', 'high'].includes(wo.priority))
    return matchesSearch && matchesStatus && matchesPriority
  })
})

const activeAssignmentForSelected = computed(() => {
  if (!selectedWorkOrder.value) return null
  return dispatchStore.assignments.find((assignment) =>
    assignment.work_order?.id === selectedWorkOrder.value?.id && activeAssignmentStatuses.has(assignment.status),
  ) ?? null
})

const selectedWorkOrderFacts = computed(() => {
  if (!selectedWorkOrder.value) return []
  const workOrder = selectedWorkOrder.value
  return [
    { label: 'Asset', value: assetLabel(workOrder) },
    { label: 'Location', value: workOrder.asset?.location || coordinatesLabel(workOrder) },
    { label: 'Outage type', value: label(workOrder.outage_type || '') },
    { label: 'Reported', value: formatDateTime(workOrder.reported_at || workOrder.created_at || null) },
    { label: 'CMMS status', value: workOrder.cmms_status || '—' },
    { label: 'Assignee', value: workOrder.assignee?.name || activeAssignmentForSelected.value?.technician?.name || 'Unassigned' },
  ]
})

watch([status, priority, search], () => {
  router.replace({
    query: {
      ...(status.value ? { status: status.value } : {}),
      ...(priority.value ? { priority: priority.value } : {}),
      ...(search.value ? { search: search.value } : {}),
    },
  })
})

onMounted(async () => {
  await refreshPage()
  const first = filteredWorkOrders.value[0] ?? workOrders.value[0] ?? null
  if (first) await selectWorkOrder(first)
})

async function refreshPage() {
  loading.value = true
  pageError.value = null
  try {
    await Promise.all([
      loadWorkOrders(),
      dispatchStore.fetchAvailability(),
      dispatchStore.fetchAssignments(),
    ])
  }
  finally {
    loading.value = false
  }
}

async function loadWorkOrders() {
  try {
    const http = createHttp(config.public.apiBase as string, authStore.token)
    const res = await http.get<PaginatedData<WorkOrderSummary>>('/work-orders', { query: { per_page: 100 } })
    workOrders.value = res.data.data
  }
  catch (err) {
    pageError.value = err instanceof ApiError ? err.message : 'Unable to load work orders'
    workOrders.value = []
  }
}

async function selectWorkOrder(workOrder: WorkOrderSummary) {
  selectedWorkOrder.value = workOrder
  assignmentNotes.value = ''
  await dispatchStore.fetchRecommendations(workOrder.id)
}

function canAssign(recommendation: DispatchRecommendation) {
  return !activeAssignmentForSelected.value && recommendation.availability === 'available'
}

async function assignRecommendation(recommendation: DispatchRecommendation) {
  if (!selectedWorkOrder.value || !canAssign(recommendation)) return

  const assignment = await dispatchStore.assign(
    selectedWorkOrder.value.id,
    recommendation.technician.id,
    assignmentNotes.value || undefined,
  )
  if (!assignment) return

  await refreshPage()
  const fresh = workOrders.value.find((wo) => wo.id === selectedWorkOrder.value?.id)
  if (fresh) await selectWorkOrder(fresh)
}

function assetLabel(workOrder: WorkOrderSummary) {
  if (!workOrder.asset) return 'No asset linked'
  return [workOrder.asset.code, workOrder.asset.name, workOrder.asset.location].filter(Boolean).join(' · ')
}

function coordinatesLabel(workOrder: WorkOrderSummary) {
  if (workOrder.latitude == null || workOrder.longitude == null) return '—'
  return `${Number(workOrder.latitude).toFixed(4)}, ${Number(workOrder.longitude).toFixed(4)}`
}

function formatDate(value: string | null) {
  if (!value) return 'Not scheduled'
  return new Intl.DateTimeFormat('en', { dateStyle: 'medium' }).format(new Date(value))
}

function formatDateTime(value: string | null) {
  if (!value) return '—'
  return new Intl.DateTimeFormat('en', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(value))
}

const HUMAN_LABELS: Record<string, string> = {
  pending_dispatch: 'Waiting for dispatch',
  imported: 'Imported from CMMS',
  assigned: 'Assigned',
  accepted: 'Accepted',
  on_the_way: 'On the way',
  arrived: 'Arrived on site',
  in_progress: 'In progress',
  completed: 'Completed',
  critical: 'Critical',
  high: 'High priority',
  medium: 'Medium priority',
  low: 'Low priority',
}

function label(value: string): string {
  if (!value) return '—'
  return HUMAN_LABELS[value.toLowerCase()] ?? value.replaceAll('_', ' ').replace(/\b\w/g, (c) => c.toUpperCase())
}

function matchLabel(score: number) {
  if (score >= 80) return 'Strong match'
  if (score >= 50) return 'Good match'
  return 'Limited match'
}

</script>
