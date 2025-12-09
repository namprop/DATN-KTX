// https://nuxt.com/docs/api/configuration/nuxt-config

import tailwindcss from "@tailwindcss/vite";
export default defineNuxtConfig({
  compatibilityDate: '2025-05-15',
  css: ['~/assets/css/main.css'],
  modules: ['@pinia/nuxt'],
  devtools: { enabled: true },
  runtimeConfig: {
    public: {
      apiBase: process.env.API_BASE || 'http://localhost:8000/api'
    }
  },
  vite: {
    plugins: [
      tailwindcss(),
    ],
  },
  app: {
    head: {
      link: [
        {
          rel: 'stylesheet',
          href: 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css'
        }
      ]
    }
  }
})
