import { defineStore } from 'pinia'
import { createAuthService } from '~/services/auth.service'
import { ApiError } from '~/types/api'
import type { AuthUser } from '~/types/auth'

const TOKEN_KEY = 'emp_auth_token'

export const useAuthStore = defineStore('auth', () => {
  const config = useRuntimeConfig()
  const baseURL = config.public.apiBase as string
  const tokenCookie = useCookie<string | null>(TOKEN_KEY, {
    maxAge: 60 * 60 * 24 * 30,
    sameSite: 'lax',
    secure: false,
  })

  const token = ref<string | null>(tokenCookie.value ?? null)
  const user = ref<AuthUser | null>(null)
  const loading = ref(false)

  // Restore token from localStorage on client boot
  if (import.meta.client) {
    const stored = localStorage.getItem(TOKEN_KEY)
    if (stored && !token.value) {
      token.value = stored
      tokenCookie.value = stored
    }
  }

  const isAuthenticated = computed(() => !!token.value)

  function _persist(value: string | null) {
    tokenCookie.value = value
    if (!import.meta.client) return
    if (value) {
      localStorage.setItem(TOKEN_KEY, value)
    }
    else {
      localStorage.removeItem(TOKEN_KEY)
    }
  }

  function _clear() {
    token.value = null
    user.value = null
    _persist(null)
  }

  function restore(): void {
    const stored = import.meta.client ? localStorage.getItem(TOKEN_KEY) : null
    const restored = token.value || tokenCookie.value || stored

    if (!restored) return

    token.value = restored
    tokenCookie.value = restored
    if (import.meta.client) {
      localStorage.setItem(TOKEN_KEY, restored)
    }
  }

  async function login(email: string, password: string): Promise<void> {
    const service = createAuthService(baseURL)
    loading.value = true
    try {
      const res = await service.login(email, password)
      token.value = res.data.token
      user.value = res.data.user
      _persist(res.data.token)
    }
    finally {
      loading.value = false
    }
  }

  async function logout(): Promise<void> {
    if (token.value) {
      const service = createAuthService(baseURL, token.value)
      try {
        await service.logout()
      }
      catch {
        // Ignore — clear locally regardless
      }
    }
    _clear()
  }

  async function fetchUser(): Promise<void> {
    if (!token.value) return
    const service = createAuthService(baseURL, token.value)
    try {
      const res = await service.me()
      user.value = res.data
    }
    catch (err) {
      if (!(err instanceof ApiError && err.status === 401)) throw err
    }
  }

  return {
    token,
    user,
    loading,
    isAuthenticated,
    restore,
    login,
    logout,
    fetchUser,
  }
})
