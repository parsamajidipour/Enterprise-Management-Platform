<template>
  <div class="rounded-lg border border-slate-200/70 bg-white/95 p-4 shadow-card ring-1 ring-white/70">
    <div class="text-xs font-black uppercase tracking-wider text-slate-500">{{ t(label) }}</div>
    <div class="mt-2 text-[15px] font-bold leading-6 text-slate-900">{{ t(value) }}</div>
  </div>
</template>

<script setup lang="ts">
import { useLanguage } from '~/composables/useLanguage'

defineProps<{
  label: string
  value: string
}>()

const { t } = useLanguage()
</script>
