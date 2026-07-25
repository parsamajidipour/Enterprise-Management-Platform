<template>
  <svg
    class="h-4 w-4"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="2"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
  >
    <path v-if="name === 'dashboard'" d="M4 13h6V4H4v9Z" />
    <path v-if="name === 'dashboard'" d="M14 20h6V4h-6v16Z" />
    <path v-if="name === 'dashboard'" d="M4 20h6v-3H4v3Z" />

    <path v-if="name === 'work-orders'" d="M9 5h10a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H9" />
    <path v-if="name === 'work-orders'" d="M5 7h8" />
    <path v-if="name === 'work-orders'" d="M5 12h8" />
    <path v-if="name === 'work-orders'" d="M5 17h8" />

    <path v-if="name === 'dispatch'" d="M7 17 3 13l4-4" />
    <path v-if="name === 'dispatch'" d="M3 13h12" />
    <path v-if="name === 'dispatch'" d="M17 7h4v10h-4" />
    <path v-if="name === 'dispatch'" d="M17 12h4" />

    <path v-if="name === 'inspections'" d="M9 3h6l1 2h3v16H5V5h3l1-2Z" />
    <path v-if="name === 'inspections'" d="m9 13 2 2 4-5" />

    <path v-if="name === 'assets'" d="M3 21h18" />
    <path v-if="name === 'assets'" d="M5 21V8l7-4 7 4v13" />
    <path v-if="name === 'assets'" d="M9 21v-6h6v6" />

    <path v-if="name === 'integrations'" d="M8 8h8v8H8z" />
    <path v-if="name === 'integrations'" d="M12 2v6" />
    <path v-if="name === 'integrations'" d="M12 16v6" />
    <path v-if="name === 'integrations'" d="M2 12h6" />
    <path v-if="name === 'integrations'" d="M16 12h6" />

    <path v-if="name === 'users'" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
    <circle v-if="name === 'users'" cx="9" cy="7" r="4" />
    <path v-if="name === 'users'" d="M22 21v-2a4 4 0 0 0-3-3.87" />
    <path v-if="name === 'users'" d="M16 3.13a4 4 0 0 1 0 7.75" />

    <path v-if="name === 'settings'" d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
    <path v-if="name === 'settings'" d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06A1.65 1.65 0 0 0 15 19.4a1.65 1.65 0 0 0-1 .6 1.65 1.65 0 0 0-.4 1.08V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 8.6 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15a1.65 1.65 0 0 0-.6-1 1.65 1.65 0 0 0-1.08-.4H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 8.6a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.6a1.65 1.65 0 0 0 1-.6 1.65 1.65 0 0 0 .4-1.08V3a2 2 0 1 1 4 0v.09A1.65 1.65 0 0 0 15.4 4.6a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9c.34.16.64.39.88.68.24.29.37.66.37 1.04V11a2 2 0 1 1 0 2h-.09A1.65 1.65 0 0 0 19.4 15Z" />
  </svg>
</template>

<script setup lang="ts">
defineProps<{
  name: string
}>()
</script>
