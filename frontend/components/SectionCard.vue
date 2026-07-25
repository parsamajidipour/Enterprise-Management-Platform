<template>
  <section class="min-w-0 rounded-lg border border-slate-200/70 bg-white/95 p-4 shadow-card ring-1 ring-white/70 backdrop-blur md:p-6">
    <div v-if="title || subtitle" class="mb-5 border-b border-slate-100 pb-4">
      <h2 class="text-lg font-black leading-7 text-slate-950 md:text-xl">{{ t(title) }}</h2>
      <p v-if="subtitle" class="mt-1 max-w-4xl text-[15px] leading-6 text-slate-500">{{ t(subtitle) }}</p>
    </div>
    <slot />
  </section>
</template>

<script setup lang="ts">
import { useLanguage } from '~/composables/useLanguage'

defineProps<{
  title?: string
  subtitle?: string
}>()

const { t } = useLanguage()
</script>
