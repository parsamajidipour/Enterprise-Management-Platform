export default defineNuxtConfig({
  compatibilityDate: '2024-09-01',
  buildDir: process.env.NUXT_BUILD_DIR || '.nuxt',

  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt',
  ],

  css: ['~/assets/css/main.css'],

  devtools: { enabled: true },

  runtimeConfig: {
    public: {
      apiBase: 'http://localhost:8010/api',
    },
  },

  app: {
    head: {
      title: 'Enterprise Management Platform',
      meta: [
        { name: 'description', content: 'Enterprise Digital Field Work Management frontend for the platform' },
      ],
      link: [
        { rel: 'shortcut icon', type: 'image/x-icon', href: '/favicon.ico' },
        { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
        { rel: 'icon', type: 'image/png', sizes: '32x32', href: '/favicon-32x32.png' },
        { rel: 'icon', type: 'image/png', sizes: '16x16', href: '/favicon-16x16.png' },
        { rel: 'apple-touch-icon', sizes: '180x180', href: '/apple-touch-icon.png' },
      ],
    },
  },
})
