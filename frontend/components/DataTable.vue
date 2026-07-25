<template>
  <div class="min-w-0 overflow-hidden rounded-lg border border-slate-200/80 bg-white shadow-card ring-1 ring-white/70">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-slate-200/80 text-sm">
        <thead class="bg-gradient-to-r from-slate-50 to-blue-50/60">
          <tr>
            <th
              v-for="column in columns"
              :key="column.key"
              class="whitespace-nowrap px-4 py-3.5 text-left text-xs font-black uppercase tracking-wider text-slate-500"
            >
              {{ t(column.label) }}
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
          <tr v-for="(row, index) in rows" :key="index" class="transition hover:bg-blue-50/50">
            <td
              v-for="column in columns"
              :key="column.key"
              class="whitespace-nowrap px-4 py-4 text-slate-700"
            >
              <StatusBadge
                v-if="column.badge"
                :label="row[column.key]"
              />
              <span v-else class="font-semibold">{{ displayText(row[column.key]) }}</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useLanguage } from '~/composables/useLanguage'

defineProps<{
  columns: Array<{ key: string; label: string; badge?: boolean }>
  rows: Record<string, unknown>[]
}>()

const { displayText, t } = useLanguage()
</script>
