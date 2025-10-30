<script setup lang="ts">
import { ref, computed, onMounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { SliderViewCandidate } from '#components'
import { useI18n } from 'vue-i18n'
import * as locales from '#b24ui/locale'
import { useColorMode } from '#imports'
import {
  getThumbnailSrc,
  formatDate,
  formatSubscriptionEndDate,
  authCheck,
} from '@/tools/cs-main'

const router = useRouter()
const { locale, locales: localesI18n, setLocale, t, defaultLocale } = useI18n()
const colorMode = useColorMode()

const $b24 = await useNuxtApp().$initializeB24Frame()
const authManager = $b24.auth
const authData = authManager.getAuthData()

const template = ref<string | null>(null)
const elId = ref<string | null>(null)

onMounted(async () => {
  if (!authData) {
    console.log('Данные аутентификации истекли или недоступны.')
    router.push('/close')
    return
  }

  const resAuthCheck = await authCheck(authData.member_id, 'hr_pro', 'hr_pro')
  if (resAuthCheck !== 'Y') {
    router.push('/close')
    return
  }

  const placement = $b24.placement
  if (placement.title === 'REST_APP_URI') {
    try {
      const options = JSON.parse(placement.options)
      if (options.type && options.type.trim() !== '') {
        template.value = options.type
        elId.value = options.id
      }
    } catch (error) {
      console.error('Ошибка при парсинге options:', error)
      router.push('/close')
    }
  }
})

const isDark = computed({
  get() {
    return colorMode.value === 'dark'
  },
  set() {
    colorMode.preference = colorMode.value === 'dark' ? 'light' : 'dark'
  }
})

const dir = computed(() => locales[locale.value]?.dir || 'ltr')

const langMap = ref(new Map(
    Object.values(localesI18n.value).map(lang => [lang.code, false])
))
langMap.value.set(locale.value, true)

const helpItems = computed(() => {
  return [
    {
      label: isDark.value ? t('page.index.settings.dark') : t('page.index.settings.light'),
      icon: isDark.value ? MoonIcon : SunIcon,
      kbds: ['shift', 'd'],
      onSelect(e: Event) {
        e?.preventDefault()
        isDark.value = !isDark.value
      }
    },
    {
      label: t('page.index.settings.currentLang', {
        code: locales[locale.value]?.code || defaultLocale,
        title: locales[locale.value]?.name || defaultLocale
      }),
      icon: EarthLanguageIcon,
      children: localesI18n.value.map((localeRow) => {
        return {
          label: localeRow.name,
          type: 'checkbox' as const,
          checked: langMap.value.get(localeRow.code),
          onUpdateChecked() {
            [...langMap.value.keys()].forEach((lang) => {
              langMap.value.set(lang, false)
            })
            langMap.value.set(localeRow.code, true)
            setLocale(localeRow.code)
            nextTick(() => {
              scrollToTop()
            })
          }
        }
      })
    }
  ]
})

onMounted(() => {
  if (locale.value?.length < 1) {
    setLocale(defaultLocale)
  }
})
</script>

<template>
<div v-if="template === 'candidate'">
    <SliderViewCandidate />
    </div>
    </template>