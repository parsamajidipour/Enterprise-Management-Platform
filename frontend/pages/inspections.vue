<template>
  <PageShell>
    <div class="mb-5 rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-900">
      Preview — demo data, this page is not yet wired to the backend API.
    </div>

    <section class="grid min-w-0 gap-5 xl:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)]">
      <SectionCard title="Smart Form Templates" subtitle="Admin library for inspection forms, validation rules, and required evidence">
        <DataTable :columns="templateColumns" :rows="formTemplates" />

        <div class="mt-5 rounded-lg border border-slate-200/70 bg-gradient-to-br from-white to-slate-50 p-4 shadow-sm ring-1 ring-white/70">
          <div class="mb-3 flex items-center justify-between">
            <h3 class="font-black text-slate-950">{{ t('Transformer Inspection Rule Set') }}</h3>
            <StatusBadge :label="tx('status.active', 'Active')" />
          </div>
          <div class="grid gap-3 md:grid-cols-2">
            <FieldPreview label="Asset ID / Name" value="Auto-filled from barcode scan" />
            <FieldPreview label="Inspector / Date" value="Auto-filled from user profile" />
            <FieldPreview label="Oil Condition" value="Dropdown rating 1-5" />
            <FieldPreview label="Temperature" value="Numeric range 0-120 C" />
            <FieldPreview label="Defect Identified" value="Require description and photo" />
            <FieldPreview label="Inspector Signature" value="Digital signature required" />
          </div>
        </div>
      </SectionCard>

      <SectionCard title="Inspection Records" subtitle="Submitted, queued, and review-required field inspections">
        <div class="grid gap-3">
          <article
            v-for="record in inspections"
            :key="record.id"
            class="rounded-lg border border-slate-200/80 bg-white p-4 shadow-sm ring-1 ring-white/70 transition hover:border-blue-200 hover:bg-blue-50/30"
          >
            <div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-100 pb-3">
              <div class="min-w-0">
                <div class="text-[11px] font-black uppercase tracking-wider text-slate-500">{{ t('Inspection') }}</div>
                <div class="mt-1 text-lg font-black leading-6 text-slate-900">{{ record.id }}</div>
              </div>

              <div class="flex flex-wrap justify-end gap-2">
                <StatusBadge :label="record.status" />
                <StatusBadge :label="record.sync" />
              </div>
            </div>

            <div class="mt-3 grid gap-3 sm:grid-cols-2 xl:grid-cols-[1.4fr_0.95fr_1.2fr_0.45fr]">
              <div class="min-w-0 rounded-lg bg-slate-50 px-3 py-2 ring-1 ring-slate-100">
                <div class="text-[11px] font-black uppercase tracking-wider text-slate-500">{{ t('Form') }}</div>
                <div class="mt-1 break-words text-sm font-black leading-5 text-slate-800">{{ displayText(record.form) }}</div>
              </div>

              <div class="min-w-0 rounded-lg bg-slate-50 px-3 py-2 ring-1 ring-slate-100">
                <div class="text-[11px] font-black uppercase tracking-wider text-slate-500">{{ t('Asset') }}</div>
                <div class="mt-1 break-words text-sm font-black leading-5 text-slate-800">{{ record.asset }}</div>
              </div>

              <div class="min-w-0 rounded-lg bg-slate-50 px-3 py-2 ring-1 ring-slate-100">
                <div class="text-[11px] font-black uppercase tracking-wider text-slate-500">{{ t('Inspector') }}</div>
                <div class="mt-1 break-words text-sm font-black leading-5 text-slate-800">{{ record.inspector }}</div>
              </div>

              <div class="min-w-0 rounded-lg bg-slate-50 px-3 py-2 ring-1 ring-slate-100">
                <div class="text-[11px] font-black uppercase tracking-wider text-slate-500">{{ t('Score') }}</div>
                <div class="mt-1 text-sm font-black leading-5 text-slate-800">{{ record.score }}</div>
              </div>
            </div>
          </article>
        </div>

        <div class="mt-5 grid gap-4 md:grid-cols-2">
          <div class="rounded-lg border border-slate-200/70 bg-white p-4 shadow-sm ring-1 ring-white/70">
            <div class="text-sm font-bold text-slate-500">{{ t('Validation Rules Passed') }}</div>
            <div class="mt-2"><ProgressBar label="Latest submitted inspection" :value="100" /></div>
          </div>
          <div class="rounded-lg border border-slate-200/70 bg-white p-4 shadow-sm ring-1 ring-white/70">
            <div class="text-sm font-bold text-slate-500">{{ t('Evidence Completeness') }}</div>
            <div class="mt-2"><ProgressBar label="Photos, GPS, signature" :value="88" /></div>
          </div>
        </div>
      </SectionCard>
    </section>
  </PageShell>
</template>

<script setup lang="ts">
import { tx, useLanguage } from '~/composables/useLanguage'
import { formTemplates, inspections } from '~/data/platform'

const { displayText, t } = useLanguage()

const templateColumns = [
  { key: 'name', label: 'Template' },
  { key: 'version', label: 'Version' },
  { key: 'fields', label: 'Fields' },
  { key: 'rules', label: 'Rules' },
  { key: 'status', label: 'Status', badge: true }
]

</script>
