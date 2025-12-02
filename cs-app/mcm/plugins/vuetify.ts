import { createVuetify } from 'vuetify'
import 'vuetify/styles' // Импорт стилей Vuetify
import { aliases, mdi } from 'vuetify/iconsets/mdi' // Для иконок Material Design

export default defineNuxtPlugin((nuxtApp) => {
    const vuetify = createVuetify({
        theme: {
            defaultTheme: 'light',
        },
        icons: {
            defaultSet: 'mdi',
            aliases,
            sets: {
                mdi,
            },
        },
    })

    nuxtApp.vueApp.use(vuetify)
})