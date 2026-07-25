import type { Config } from 'tailwindcss'

export default <Partial<Config>>{
  content: [
    './components/**/*.{vue,js,ts}',
    './layouts/**/*.vue',
    './pages/**/*.vue',
    './app.vue'
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          navy: '#101828',
          blue: '#2563eb',
          cyan: '#0891b2',
          green: '#059669',
          orange: '#d97706',
          red: '#dc2626'
        },
        surface: {
          page: '#f6f8fb',
          panel: '#ffffff',
          muted: '#f8fafc',
          sidebar: '#0f172a',
          sidebarAlt: '#111827'
        }
      },
      boxShadow: {
        soft: '0 20px 48px rgba(15, 23, 42, 0.10)',
        card: '0 1px 2px rgba(16, 24, 40, 0.04), 0 8px 24px rgba(16, 24, 40, 0.06)',
        focus: '0 0 0 4px rgba(37, 99, 235, 0.14)'
      }
    }
  }
}
