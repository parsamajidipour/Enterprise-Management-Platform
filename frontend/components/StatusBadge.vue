<template>
  <span :class="badgeClass">{{ displayText(label) }}</span>
</template>

<script setup lang="ts">
import { useLanguage } from '~/composables/useLanguage'

const props = defineProps<{ label: unknown }>()
const { displayText } = useLanguage()

const badgeClass = computed(() => {
  const base = 'inline-flex max-w-full items-center whitespace-nowrap rounded-full border px-3 py-1 text-xs font-black leading-4 shadow-sm'
  const key = props.label && typeof props.label === 'object' && 'key' in props.label
    ? String((props.label as { key: string }).key)
    : ''
  const value = key || String(props.label ?? '').toLowerCase()

  if (value.includes('completed') || value.includes('approved') || value.includes('submitted') || value.includes('synced') || value.includes('connected') || value.includes('healthy') || value.includes('active')) {
    return `${base} border-emerald-200 bg-emerald-50 text-emerald-700`
  }
  if (value.includes('progress') || value.includes('assigned')) {
    return `${base} border-blue-200 bg-blue-50 text-blue-700`
  }
  if (value.includes('offline') || value.includes('queued') || value.includes('pending') || value.includes('monitoring') || value.includes('draft')) {
    return `${base} border-amber-200 bg-amber-50 text-amber-700`
  }
  if (value.includes('critical') || value.includes('review')) {
    return `${base} border-red-200 bg-red-50 text-red-700`
  }
  if (value.includes('high')) {
    return `${base} border-amber-200 bg-amber-50 text-amber-700`
  }

  return `${base} border-slate-200 bg-slate-50 text-slate-700`
})
</script>
