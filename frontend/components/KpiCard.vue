<template>
  <div class="group relative overflow-hidden rounded-lg border border-slate-200/70 bg-white/95 p-5 shadow-card ring-1 ring-white/70 backdrop-blur transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-soft">
    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-blue-600 via-cyan-500 to-emerald-500 opacity-80" />
    <div class="flex items-start justify-between gap-4">
      <div>
        <p class="text-[15px] font-bold text-slate-500">{{ t(item.title) }}</p>
        <div class="mt-2 text-3xl font-black leading-9 text-slate-950">{{ item.value }}</div>
      </div>
      <div :class="badgeClass">{{ item.icon }}</div>
    </div>
    <div class="mt-5 flex items-center justify-between gap-3 text-sm">
      <span class="text-slate-500">{{ t('Target') }}: <b class="text-slate-700">{{ t(item.target) }}</b></span>
      <span class="font-black text-emerald-600">{{ item.trend }}</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useLanguage } from '~/composables/useLanguage'

const props = defineProps<{ item: any }>()
const { t } = useLanguage()

const badgeClass = computed(() => {
  const base = 'flex h-12 w-12 items-center justify-center rounded-lg border text-sm font-black shadow-sm transition group-hover:scale-105'
  if (props.item.tone === 'green') return `${base} border-emerald-200 bg-emerald-50 text-emerald-700`
  if (props.item.tone === 'orange') return `${base} border-amber-200 bg-amber-50 text-amber-700`
  return `${base} border-blue-200 bg-blue-50 text-blue-700`
})
</script>
