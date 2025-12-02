import tailwindcss from '@tailwindcss/vite'
import { readFileSync } from 'node:fs'
import { contentLocales } from './i18n.map'

// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  modules: [
    '@bitrix24/b24ui-nuxt',
    '@nuxt/eslint',
    '@nuxt/content',
    '@nuxtjs/i18n',
    '@pinia/nuxt',
    '@bitrix24/b24jssdk-nuxt'
  ],
  ssr: false,
  /**
   * @memo App work under frame
   * Nuxt DevTools: Failed to check parent window
   * SecurityError: Failed to read a named property '__NUXT_DEVTOOLS_DISABLE__' from 'Window'
   */
  devtools: { enabled: false },
app:{
  baseURL:'/cs-app/mcm/',
  buildAssetsDir:"/cs-app/mcm/.nuxt/"
},
  router: {
    base: '/cs-app/mcm/'
  },
  // dirs: {
  //   components: ['~/components', '~/cs-components'] //
  // },
  css: [
      '~/assets/css/main.css',
    //'vuetify/styles', // Основные стили Vuetify
   // '@mdi/font/css/materialdesignicons.css' // Иконки Material Design

  ],
  build: {
    transpile: ['vuetify'], // Транспиляция Vuetify
  },

  content: {
    watch: {
      enabled: false
    }
  },
  runtimeConfig: {
    public: {
      contentLocales: contentLocales
    }
  },
  devServer: {
    port: 3002,
    host: '0.0.0.0',
    loadingTemplate: () => {
      return readFileSync('./template/devServer-loading.html', 'utf-8')
    }
  },

  future: {
    compatibilityVersion: 4
  },

  compatibilityDate: '2025-05-01',

  vite: {
    plugins: [
      tailwindcss()
    ],
    define: {
      'process.env.DEBUG': false, // Исправление для некоторых библиотек
    },
    server: {
      hmr: {
        host: 'app.cassoft.ru',
        port: 443, // Используйте 443 для HTTPS или 80 для HTTP
        protocol: 'wss' // Используйте 'wss' для HTTPS или 'ws' для HTTP
      },
      allowedHosts: ['app.cassoft.ru']
    }
  },
  i18n: {
    bundle: {
      optimizeTranslationDirective: false
    },
    detectBrowserLanguage: false,
    strategy: 'no_prefix',
    lazy: true,
    defaultLocale: 'ru',
    locales: contentLocales
  },
 // plugins: [
 //  // '@/plugins/globals.ts',
 //   '@/plugins/vuetify.ts',
 //   //'@/plugins/utils.ts',
 // ],
})
