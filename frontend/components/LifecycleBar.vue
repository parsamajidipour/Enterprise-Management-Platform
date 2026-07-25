<template>
  <div class="flex items-center gap-1 overflow-x-auto">
    <template v-for="(stage, idx) in stages" :key="stage.key">
      <div class="flex min-w-0 flex-1 flex-col items-center gap-1">
        <div
          class="h-1.5 w-full rounded-full"
          :class="stageOrder(stage.key) <= currentOrder ? 'bg-blue-600' : 'bg-slate-200'"
        />
        <span
          class="text-center text-[10px] font-black leading-tight"
          :class="stage.key === normalizedStatus ? 'text-blue-700' : stageOrder(stage.key) < currentOrder ? 'text-slate-500' : 'text-slate-300'"
        >
          {{ stage.label }}
        </span>
      </div>
      <div v-if="idx < stages.length - 1" class="h-px w-2 shrink-0 bg-slate-200" />
    </template>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  status: string
}>()

const stages = [
  { key: 'assigned', label: 'Assigned' },
  { key: 'accepted', label: 'Accepted' },
  { key: 'on_the_way', label: 'On way' },
  { key: 'arrived', label: 'Arrived' },
  { key: 'in_progress', label: 'Working' },
]

const order: Record<string, number> = {
  created: 0,
  sent_to_technician: 0,
  assigned: 0,
  accepted: 1,
  on_the_way: 2,
  arrived: 3,
  in_progress: 4,
  completed: 5,
}

const normalizedStatus = computed(() => {
  if (props.status === 'created' || props.status === 'sent_to_technician') return 'assigned'
  return props.status
})

const currentOrder = computed(() => order[props.status] ?? order[normalizedStatus.value] ?? -1)

function stageOrder(stage: string) {
  return order[stage] ?? -1
}
</script>
