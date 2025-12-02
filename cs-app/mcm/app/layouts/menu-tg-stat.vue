<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { SettingsSlider, IntegratorNav, Logo , ModalLoader, ActivityItemModalConfirm,} from '#components'
import { EActivityCategory } from '~/types'
import TelegramInCircleIcon from '@bitrix24/b24icons-vue/social/TelegramInCircleIcon'
import type { NavigationMenuItem } from '@bitrix24/b24ui-nuxt'
import { useUserSettingsStore } from '~/stores/userSettings'
import { useAppSettingsStore } from '~/stores/appSettings'
import PulseCircleIcon from '@bitrix24/b24icons-vue/main/PulseCircleIcon'
import SettingsIcon from '@bitrix24/b24icons-vue/common-service/SettingsIcon'
import SunIcon from '@bitrix24/b24icons-vue/main/SunIcon'
import MoonIcon from '@bitrix24/b24icons-vue/main/MoonIcon'
import HelpIcon from '@bitrix24/b24icons-vue/main/HelpIcon'
import MessageChatWithPointIcon from '@bitrix24/b24icons-vue/main/MessageChatWithPointIcon'
import MailSentIcon from '@bitrix24/b24icons-vue/contact-center/MailSentIcon'
import BusinessProcessIcon from '@bitrix24/b24icons-vue/main/BusinessProcessIcon'

import { initializeB24Frame, B24Frame } from '@bitrix24/b24jssdk';

let $b24: B24Frame | null = null;
const currentUser = ref(null);
onMounted(async () => {
  try {
    $b24 = await initializeB24Frame();
    // Получаем данные о текущем пользователе
    if ($b24) {
      const resUser = await $b24.callMethod('user.current');
      const user = resUser.getData().result
      //  console.log(user)
      currentUser.value = user;
      console.log('Текущий пользователь:', currentUser.value.LAST_NAME);
    }
  } catch (error) {
    console.error('Ошибка инициализации:', error);
  }
});

onUnmounted(() => {
  $b24?.destroy();
});

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
const items = [
  {
    label: 'Статистика',
    icon: MessageChatWithPointIcon,
    to: '/tgstat/statistics'
  },
  {
    label: 'История событий',
    icon: SettingsIcon,
    to: '/tgstat/events'
  },
  {
    label: 'Отчеты (скоро)',
    icon: MailSentIcon,
    disabled: true,
    to: '/mailing-lists'
  },


] satisfies NavigationMenuItem[]


const helpItems = computed(() => {
  const result: NavigationMenuItem[] = []
  if (currentUser.value?.LAST_NAME === 'Черкасов' ||currentUser.value?.LAST_NAME === 'CAS-soft') {
    result.push({
      label: t('component.nav.settings.settings'),
      icon: SettingsIcon,
      async onSelect(e: Event) {
        e?.preventDefault()
        await settingsSlider.open()
      }
    })

    result.push({
      label: t('component.nav.settings.help'),
      icon: HelpIcon,
      to: 'https://cassoft.ru/',
      target: '_blank'
    })

    result.push({
      label: t('component.nav.settings.support'),
      icon: PulseCircleIcon,
      to: 'https://cassoft.ru/',
      target: '_blank'
    })
    //  if (currentUser.LAST_NAME === 'Черкасов') {
    result.push({
      label: "support",
      icon: PulseCircleIcon,
      to: '/tgstat/supportTgStat',
    });

  result.push({
      label: "table",
      icon: PulseCircleIcon,
      to: '/tgstat/table',
    });
  }
  result.push({
    label: isDark.value ? t('component.nav.settings.dark') : t('component.nav.settings.light'),
    icon: isDark.value ? MoonIcon : SunIcon,
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
  <B24SidebarLayout
      :use-light-content="false"
  >
    <template #sidebar="{ handleClick }" style="margin-right: 10px">
      <B24SidebarHeader>
        <B24SidebarSection class="ps-[18px] flex-row  items-center justify-start gap-0.5 text-primary">
          <TelegramInCircleIcon class="w-7 h-7" />
          <ProseH4 class="mb-0 leading-none">
            {{ $t('appTgStat.title') }}
          </ProseH4>
        </B24SidebarSection>
      </B24SidebarHeader>
      <B24SidebarBody>
        <B24NavigationMenu
            :items="items"
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
        <IntegratorNav />
      </B24NavbarSection>
    </template>

    <!-- Header -->
    <div>
      <slot name="header-title">
        <!-- / header-title / -->
      </slot>
    </div>

    <!-- Content -->
    <div class="p-4">
      <NuxtPage />
    </div>
  </B24SidebarLayout>
</template>
