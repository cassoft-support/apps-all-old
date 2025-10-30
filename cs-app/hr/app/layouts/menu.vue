<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed, nextTick} from 'vue'
import { SettingsSlider, IntegratorNav, Logo, ModalLoader } from '#components'
import type { NavigationMenuItem } from '@bitrix24/b24ui-nuxt'
import PulseCircleIcon from '@bitrix24/b24icons-vue/main/PulseCircleIcon'
import SettingsIcon from '@bitrix24/b24icons-vue/common-service/SettingsIcon'
import SunIcon from '@bitrix24/b24icons-vue/main/SunIcon'
import MoonIcon from '@bitrix24/b24icons-vue/main/MoonIcon'
import HelpIcon from '@bitrix24/b24icons-vue/main/HelpIcon'
import MessageChatWithPointIcon from '@bitrix24/b24icons-vue/main/MessageChatWithPointIcon'
import MailSentIcon from '@bitrix24/b24icons-vue/contact-center/MailSentIcon'
import BusinessProcessIcon from '@bitrix24/b24icons-vue/main/BusinessProcessIcon'

import { initializeB24Frame, B24Frame } from '@bitrix24/b24jssdk'
import { useRouter, useRoute } from 'vue-router'
const router = useRouter()
const route = useRoute()

let $b24: B24Frame | null = null
const currentUser = ref(null)


// Функция для открытия пункта меню при загрузке


const openSupportMenu = async () => {
  await nextTick()
  const supportItem = document.querySelector('a[href="/vacancy"]')
  if (supportItem) {
    supportItem.click()
  }
}
onMounted(async () => {
  try {
    $b24 = await initializeB24Frame()
    if ($b24) {
      const resUser = await $b24.callMethod('user.current')
      const user = resUser.getData().result
      currentUser.value = user
    }

    // Открываем пункт меню при загрузке
    openSupportMenu()
  } catch (error) {
    console.error('Ошибка инициализации:', error)
  }
})

onUnmounted(() => {
  $b24?.destroy()
})

const { t } = useI18n()
const colorMode = useColorMode()
const overlay = useOverlay()
const settingsSlider = overlay.create(SettingsSlider)

useHead({
  bodyAttrs: {
    class: 'text-base-master dark:text-base-150 bg-base-50 dark:bg-base-dark font-b24-system antialiased'
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

const appSettings = useAppSettingsStore()
const userSettings = useUserSettingsStore()

// Основные пункты меню
const items = [
  {
    label: 'Вакансии',
    to: '/vacancy',
    icon: MessageChatWithPointIcon,
    defaultOpen: true,
  },
  {
    label: 'Размещение',
    to: '/ads-control',
    icon: MessageChatWithPointIcon,
    defaultOpen: true,
  },{
    label: 'Отчет по размещению',
    to: '/ads-report',
    icon: MessageChatWithPointIcon,
    defaultOpen: true,
  },
  {
    label: 'Соискатели',
    to: '/candidate',
    icon: h(SettingsIcon, { width: '24', height: '24' }),
  }
] satisfies NavigationMenuItem[]

// Дополнительные пункты меню
const helpItems = computed(() => {
  const result: NavigationMenuItem[] = []

  result.push({
    label: t('component.nav.settings.settings'),
    icon: h(SettingsIcon, { width: '24', height: '24' }),
    to:'/settings'
  })

  result.push({
    label: t('component.nav.settings.help'),
    icon: h(HelpIcon, { width: '24', height: '24' }),
    to: 'https://cassoft.ru/',
    target: '_blank'
  })

  result.push({
    label: t('component.nav.settings.support'),
    icon: h(PulseCircleIcon, { width: '24', height: '24' }),
    to: 'https://cassoft.ru/',
    target: '_blank'
  })

  if (currentUser.value && currentUser.value.LAST_NAME === 'Черкасов') {
    result.push({
      label: 'support',
      icon: h(PulseCircleIcon, { width: '24', height: '24' }),
      to: '/support',
    });
  }

  result.push({
    label: isDark.value ? t('component.nav.settings.dark') : t('component.nav.settings.light'),
    icon: h(isDark.value ? MoonIcon : SunIcon, { width: '24', height: '24' }),
    kbds: ['shift', 'd'],
    badge: {
      label: 'shift + d',
      color: 'default' as const,
      depth: 'light' as const,
      useFill: false
    },
    onSelect(e: Event) {
      e?.preventDefault()
      isDark.value = !isDark.value
    }
  })

  return result
})

defineShortcuts(extractShortcuts(helpItems.value))
</script>
<template>
<B24SidebarLayout :use-light-content="false">
<template #sidebar="{ handleClick }" style="margin-right: 10px">
  <B24SidebarHeader>
    <B24SidebarSection class="ps-[18px] flex-row items-center justify-start gap-0.5 text-primary">
      <Logo class="size-10" />
      <ProseH4 class="mb-0 leading-none">
        {{ $t('app.title') }}
      </ProseH4>
    </B24SidebarSection>
  </B24SidebarHeader>
  <B24SidebarBody>
    <B24NavigationMenu
        :items="items"
        variant="pill"
        orientation="vertical"
        overflow="hidden"
    />
    <B24SidebarSpacer />
    <B24NavigationMenu
        :items="helpItems"
        variant="pill"
        orientation="vertical"
        overflow="hidden"
    />
  </B24SidebarBody>
  <B24SidebarFooter style="display: none">
    <IntegratorNav />
  </B24SidebarFooter>
</template>
<template #navbar>
  <B24NavbarSpacer />
  <B24NavbarSection>
    <!-- Здесь можно разместить дополнительные элементы -->
  </B24NavbarSection>
</template>

<!-- Header -->
<div>
  <slot name="header-title">

  </slot>
</div>

<!-- Content -->
  <div class="p-4">
    <NuxtPage />
  </div>
</B24SidebarLayout>
</template>