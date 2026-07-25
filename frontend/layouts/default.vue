<template>
  <div class="min-h-screen bg-surface-page text-slate-700">
    <aside
      class="no-print fixed inset-y-0 left-0 z-40 hidden w-72 border-r border-white/10 bg-[linear-gradient(180deg,#0b1220_0%,#111827_48%,#0f172a_100%)] shadow-2xl shadow-slate-950/20 lg:block"
    >
      <div
        class="flex items-center gap-3 border-b border-white/10 bg-white/[0.045] px-5"
        height=88px
      >
        <div>
          <img src="/logo.png" alt="EMP" width=88px />
        </div>
        <div class="min-w-0">
          <div class="text-xs font-black uppercase tracking-[0.16em] text-slate-400">{{ t('EMP') }}</div>
          <div class="truncate text-lg font-black leading-6 text-white">{{ t('Admin Panel') }}</div>
        </div>
      </div>

      <nav class="space-y-1.5 p-3.5">
        <NuxtLink
          v-for="item in navItems"
          :key="item.to"
          :to="item.to"
          class="group flex items-center gap-3 rounded-lg border border-transparent px-3 py-3 text-[15px] font-bold leading-6 text-slate-300 outline-none transition hover:border-white/10 hover:bg-white/[0.085] hover:text-white focus-visible:border-blue-300/70 focus-visible:ring-2 focus-visible:ring-blue-400/30"
          active-class="border-blue-300/30 bg-gradient-to-r from-blue-600 to-cyan-600 text-white shadow-soft shadow-blue-950/30 hover:from-blue-600 hover:to-cyan-600 hover:text-white"
        >
          <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/10 text-slate-200 ring-1 ring-white/10 transition group-hover:bg-white/15 group-hover:text-white">
            <NavIcon :name="item.icon" />
          </span>
          <span class="truncate">{{ t(item.title) }}</span>
        </NuxtLink>
      </nav>

      <div class="absolute bottom-4 left-4 right-4 rounded-lg border border-white/10 bg-white/[0.07] p-4 text-xs text-slate-300 shadow-soft ring-1 ring-white/[0.04] backdrop-blur">
        <b class="text-xs font-black uppercase tracking-[0.16em] text-white">{{ t('Admin scope') }}</b>
        <p class="mt-1 text-[13px] leading-5">{{ t('Manage work orders, forms, assets, integrations, roles, and platform settings.') }}</p>
      </div>
    </aside>

    <div class="lg:pl-72">
      <header class="no-print sticky top-0 z-30 border-b border-slate-200/80 bg-white/90 shadow-[0_1px_0_rgba(15,23,42,0.02),0_14px_34px_rgba(15,23,42,0.06)] backdrop-blur-xl">
        <div class="flex min-h-[88px] flex-wrap items-center justify-between gap-4 px-4 py-3 md:px-7">
          <div class="min-w-0">
            <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-500">{{ t('Digital Field Work Management') }}</p>
            <h1 class="mt-1 truncate text-2xl font-black leading-8 text-slate-950 md:text-3xl">{{ pageTitle }}</h1>
          </div>

          <div class="header-actions flex items-center gap-2">
            <div class="hidden min-w-0 flex-col items-end sm:flex">
              <span class="max-w-48 truncate text-sm font-black text-slate-900">{{ authStore.user?.name || 'Operations User' }}</span>
              <span class="max-w-48 truncate text-xs font-semibold text-slate-500">{{ authStore.user?.email || 'Signed in' }}</span>
            </div>

            <button
              type="button"
              class="language-switch relative inline-flex h-11 w-[128px] items-center rounded-full border border-slate-200 bg-slate-100/90 p-1 text-xs font-black text-slate-500 shadow-sm outline-none ring-1 ring-white transition hover:border-blue-300 hover:bg-blue-50/50 hover:shadow-soft focus-visible:border-blue-400 focus-visible:ring-4 focus-visible:ring-blue-100"
              :aria-label="isArabic ? t('English') : t('Arabic')"
              :aria-pressed="isArabic"
              @click="toggleLocale"
            >
              <span
                class="absolute top-1 h-9 w-[58px] rounded-full bg-white shadow-sm ring-1 ring-slate-200/80 transition-transform duration-200"
                :class="isArabic ? 'translate-x-[60px]' : 'translate-x-0'"
              />
              <span class="relative z-10 flex w-1/2 justify-center transition-colors" :class="!isArabic ? 'text-blue-700' : ''">EN</span>
              <span class="relative z-10 flex w-1/2 justify-center transition-colors" :class="isArabic ? 'text-blue-700' : ''">AR</span>
            </button>

            <button
              type="button"
              class="inline-flex h-11 items-center gap-2 rounded-lg border border-red-200 bg-white px-4 text-sm font-black text-red-700 shadow-sm outline-none transition hover:border-red-300 hover:bg-red-50 focus-visible:border-red-400 focus-visible:ring-4 focus-visible:ring-red-100 disabled:cursor-wait disabled:opacity-60"
              :disabled="loggingOut"
              @click="handleLogout"
            >
              <svg
                v-if="loggingOut"
                class="h-4 w-4 animate-spin"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                <path d="M16 17l5-5-5-5" />
                <path d="M21 12H9" />
              </svg>
              <span>Logout</span>
            </button>
          </div>
        </div>
      </header>
      <slot />
    </div>
  </div>
</template>

<script setup lang="ts">
import { useLanguage } from '~/composables/useLanguage'
import { navItems } from '~/data/platform'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const { dir, isArabic, lang, t, toggleLocale } = useLanguage()
const loggingOut = ref(false)

const sidebarBrandHeightPx = 104

useHead({
  htmlAttrs: {
    dir,
    lang
  }
})

const pageTitle = computed(() => {
  const match = navItems.find((item) => item.to === route.path)
  return t(match?.title ?? 'Enterprise Management Platform')
})

onMounted(() => {
  authStore.fetchUser()
})

async function handleLogout() {
  if (loggingOut.value) return

  loggingOut.value = true
  try {
    await authStore.logout()
    await router.replace('/login')
  }
  finally {
    loggingOut.value = false
  }
}
</script>
