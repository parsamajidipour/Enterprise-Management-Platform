<template>
  <PageShell>
    <section class="grid gap-5 xl:grid-cols-[1.2fr_0.8fr]">
      <SectionCard title="Asset Master Registry" subtitle="Barcode, GIS location, inspection counts, defects, and condition index">
        <DataTable :columns="columns" :rows="assets" />
      </SectionCard>

      <SectionCard title="Condition Index">
        <div class="space-y-4">
          <div v-for="asset in assets" :key="asset.id" class="rounded-lg border border-slate-200/70 bg-gradient-to-br from-white to-slate-50 p-4 shadow-sm ring-1 ring-white/70">
            <div class="mb-3 flex items-center justify-between">
              <div>
                <h3 class="font-black text-slate-950">{{ asset.id }}</h3>
                <p class="text-sm text-slate-500">{{ t(asset.category) }}</p>
              </div>
              <StatusBadge :label="asset.status" />
            </div>
            <ProgressBar label="Health Index" :value="asset.health" />
          </div>
        </div>
      </SectionCard>
    </section>

    <SectionCard class="mt-5" title="Asset Categories" subtitle="Initial scope from the proposal; update frequencies as platform policies change">
      <DataTable :columns="categoryColumns" :rows="assetCategories" />
    </SectionCard>
  </PageShell>
</template>

<script setup lang="ts">
import { useLanguage } from '~/composables/useLanguage'
import { assetCategories, assets } from '~/data/platform'

const { t } = useLanguage()

const categoryColumns = [
  { key: 'category', label: 'Asset Category' },
  { key: 'frequency', label: 'Inspection Frequency' },
  { key: 'priority', label: 'Priority', badge: true },
  { key: 'forms', label: 'Form Templates' }
]

const columns = [
  { key: 'id', label: 'Asset ID' },
  { key: 'name', label: 'Name' },
  { key: 'category', label: 'Category' },
  { key: 'health', label: 'Health' },
  { key: 'barcode', label: 'Barcode' },
  { key: 'gis', label: 'GIS Location' },
  { key: 'inspections', label: 'Inspections' },
  { key: 'defects', label: 'Defects' },
  { key: 'status', label: 'Status', badge: true }
]
</script>
