<template>
  <div class="relative min-h-screen overflow-hidden bg-[#06132a] text-white">
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_22%_12%,rgba(37,99,235,0.32),transparent_34%),radial-gradient(circle_at_72%_44%,rgba(14,165,233,0.16),transparent_33%),linear-gradient(115deg,#06132a_0%,#08234a_46%,#051227_100%)]" />
    <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(rgba(96,165,250,0.045)_1px,transparent_1px),linear-gradient(90deg,rgba(96,165,250,0.045)_1px,transparent_1px)] bg-[size:58px_58px] opacity-70" />
    <div class="pointer-events-none absolute left-1/2 top-1/2 h-[38rem] w-[38rem] -translate-x-1/2 -translate-y-1/2 rounded-full border border-blue-300/10" />
    <div class="pointer-events-none absolute left-1/2 top-1/2 h-[28rem] w-[28rem] -translate-x-1/2 -translate-y-1/2 rounded-full border border-cyan-300/10" />
    <div class="pointer-events-none absolute bottom-0 left-0 right-0 h-40 bg-gradient-to-t from-[#041026] to-transparent" />

    <main class="relative z-10 flex min-h-screen items-center justify-center px-5 py-8 sm:px-8">
      <section class="w-full max-w-[29rem]">
        <div class="mb-7 flex justify-center">
          <div class="flex h-24 w-24 items-center justify-center rounded-[1.35rem] border border-white/15 bg-white/[0.08] shadow-[0_24px_70px_rgba(2,8,23,0.45)] ring-1 ring-white/[0.06] backdrop-blur-xl">
            <img src="/logo.png" alt="EMP" class="h-16 w-16 rounded-full drop-shadow-[0_0_26px_rgba(96,165,250,0.32)]" />
          </div>
        </div>

        <div class="rounded-[1.4rem] border border-blue-100/20 bg-[#071934]/82 p-6 shadow-[0_34px_110px_rgba(0,0,0,0.48)] ring-1 ring-white/[0.05] backdrop-blur-2xl sm:p-8">
          <div class="mb-7 text-center">
            <p class="text-xs font-black uppercase tracking-[0.28em] text-blue-200">EMP</p>
            <h1 class="mt-3 text-3xl font-black tracking-normal text-white">Operations Center</h1>
            <div class="mx-auto mt-4 h-0.5 w-12 rounded-full bg-gradient-to-r from-blue-400 to-cyan-300 shadow-[0_0_22px_rgba(56,189,248,0.75)]" />
          </div>

            <form class="space-y-4" novalidate @submit.prevent="handleSubmit">
              <div>
                <label for="email" class="mb-2.5 block text-sm font-bold text-slate-100">
                  Email Address
                </label>
                <div class="relative">
                  <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="5" width="18" height="14" rx="2" />
                    <path d="M3 7l9 6 9-6" />
                  </svg>
                  <input
                    id="email"
                    v-model.trim="form.email"
                    type="email"
                    name="email"
                    autocomplete="email"
                    placeholder="Enter your email"
                    class="block h-14 w-full rounded-lg border border-blue-100/20 bg-[#06152d]/55 px-14 text-base font-medium text-white outline-none transition placeholder:text-slate-500 focus:border-blue-300/70 focus:bg-[#071936]/75 focus:ring-4 focus:ring-blue-400/15 disabled:opacity-50"
                    :class="{ 'border-red-400/70 focus:border-red-400/70 focus:ring-red-400/15': errors.email }"
                    :disabled="loading"
                    @blur="touchField('email')"
                  />
                </div>
                <p v-if="errors.email" class="mt-2 text-xs font-bold text-red-300">
                  {{ errors.email }}
                </p>
              </div>

              <div>
                <label for="password" class="mb-2.5 block text-sm font-bold text-slate-100">
                  Password
                </label>
                <div class="relative">
                  <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="5" y="11" width="14" height="10" rx="2" />
                    <path d="M8 11V7a4 4 0 018 0v4" />
                  </svg>
                  <input
                    id="password"
                    v-model="form.password"
                    :type="showPassword ? 'text' : 'password'"
                    name="password"
                    autocomplete="current-password"
                    placeholder="Enter your password"
                    class="block h-14 w-full rounded-lg border border-blue-100/20 bg-[#06152d]/55 px-14 pr-14 text-base font-medium text-white outline-none transition placeholder:text-slate-500 focus:border-blue-300/70 focus:bg-[#071936]/75 focus:ring-4 focus:ring-blue-400/15 disabled:opacity-50"
                    :class="{ 'border-red-400/70 focus:border-red-400/70 focus:ring-red-400/15': errors.password }"
                    :disabled="loading"
                    @blur="touchField('password')"
                  />
                  <button
                    type="button"
                    class="absolute right-4 top-1/2 -translate-y-1/2 rounded-lg p-1 text-slate-400 transition hover:bg-white/10 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300/40"
                    :aria-label="showPassword ? 'Hide password' : 'Show password'"
                    tabindex="-1"
                    @click="showPassword = !showPassword"
                  >
                    <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94" />
                      <path d="M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19" />
                      <line x1="1" y1="1" x2="23" y2="23" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                      <circle cx="12" cy="12" r="3" />
                    </svg>
                  </button>
                </div>
                <p v-if="errors.password" class="mt-2 text-xs font-bold text-red-300">
                  {{ errors.password }}
                </p>
              </div>

              <div
                v-if="serverError"
                role="alert"
                class="flex items-start gap-3 rounded-lg border border-red-400/25 bg-red-500/10 px-4 py-3 text-sm font-semibold text-red-200"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10" /><line x1="12" y1="8" x2="12" y2="12" /><line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
                <span>{{ serverError }}</span>
              </div>

              <button
                type="submit"
                class="relative mt-3 flex h-14 w-full items-center justify-center gap-4 overflow-hidden rounded-lg bg-gradient-to-r from-blue-500 to-blue-700 px-6 text-base font-black text-white shadow-[0_18px_48px_rgba(37,99,235,0.32)] outline-none transition hover:from-blue-400 hover:to-blue-600 hover:shadow-[0_22px_58px_rgba(37,99,235,0.42)] focus-visible:ring-4 focus-visible:ring-blue-300/25 active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-60"
                :disabled="loading"
              >
                <span class="absolute inset-0 bg-white/0 transition hover:bg-white/10" />
                <svg
                  v-if="loading"
                  class="relative h-4 w-4 animate-spin"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
                <span class="relative">{{ loading ? 'Verifying credentials...' : 'Access Operations Center' }}</span>
                <svg v-if="!loading" xmlns="http://www.w3.org/2000/svg" class="relative h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12h14" />
                  <path d="M13 5l7 7-7 7" />
                </svg>
              </button>
            </form>

            <div class="mt-7 flex items-center justify-center border-t border-white/12 pt-5 text-xs text-slate-400">
              <div class="flex items-center gap-2 rounded-full border border-lime-300/20 bg-lime-300/10 px-3 py-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-lime-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                  <path d="M9 12l2 2 4-5" />
                </svg>
                <span class="font-black text-lime-200">Government Use Only</span>
              </div>
            </div>
          </div>
      </section>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ApiError } from '~/types/api'

definePageMeta({ layout: false })

useHead({
  title: 'Enterprise Management Platform | Operations Center',
})

const authStore = useAuthStore()
const router = useRouter()
authStore.restore()

// Redirect already-authenticated users
if (import.meta.client && authStore.isAuthenticated) {
  router.replace('/')
}

const form = reactive({ email: '', password: '' })
const touched = reactive({ email: false, password: false })
const showPassword = ref(false)
const serverError = ref('')
const loading = computed(() => authStore.loading)

const errors = computed(() => {
  const e: Record<string, string> = {}
  if (touched.email) {
    if (!form.email) e.email = 'Email is required.'
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) e.email = 'Enter a valid email address.'
  }
  if (touched.password) {
    if (!form.password) e.password = 'Password is required.'
    else if (form.password.length < 6) e.password = 'Password must be at least 6 characters.'
  }
  return e
})

function touchField(field: 'email' | 'password') {
  touched[field] = true
}

function isFormValid(): boolean {
  touched.email = true
  touched.password = true
  return Object.keys(errors.value).length === 0
}

async function handleSubmit() {
  serverError.value = ''
  if (!isFormValid()) return

  try {
    await authStore.login(form.email, form.password)
    await router.replace('/')
  }
  catch (err) {
    if (err instanceof ApiError) {
      serverError.value = err.message
    }
    else {
      serverError.value = 'An unexpected error occurred. Please try again.'
    }
  }
}
</script>
