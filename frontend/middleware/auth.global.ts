export default defineNuxtRouteMiddleware((to) => {
  // /login is always public
  if (to.path === '/login') return

  const authStore = useAuthStore()
  authStore.restore()

  // SSR: allow rendering — client will redirect if needed
  if (import.meta.server) return

  // Client: redirect unauthenticated users to login
  if (!authStore.isAuthenticated) {
    return navigateTo('/login', { replace: true })
  }
})
