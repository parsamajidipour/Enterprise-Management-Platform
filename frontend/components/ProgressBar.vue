<template>
  <div>
    <div class="mb-1.5 flex items-center justify-between text-xs font-bold text-slate-600">
      <span>{{ t(label) }}</span>
      <span>{{ value }}%</span>
    </div>
    <div class="h-2.5 overflow-hidden rounded-full bg-slate-200 shadow-inner">
      <div
        class="progress-bar-fill h-full rounded-full bg-gradient-to-r from-blue-600 via-cyan-500 to-emerald-500 transition-[width,margin] duration-200 ease-out"
        :style="{ width: `${value}%` }"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { useLanguage } from '~/composables/useLanguage'

defineProps<{
  label: string
  value: number
}>()

const { t } = useLanguage()
</script>
