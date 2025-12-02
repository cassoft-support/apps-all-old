<script setup lang="ts">
import type { ButtonProps } from '@bitrix24/b24ui-nuxt/types/index.ts'
import type { DescriptionListItem } from '@bitrix24/b24ui-nuxt/components/DescriptionList.vue'
import SignIcon from '@bitrix24/b24icons-vue/main/SignIcon'
import DotsIcon from '@bitrix24/b24icons-vue/button/DotsIcon'

const action = (color: string): ButtonProps[] => [
  {
    icon: DotsIcon,
    color: color as any,
    depth: 'light',
    onClick() {
      console.log('Action clicked')
    }
  }
]



const itemsActions: DescriptionListItem[] = [
  {
    label: 'iOS',
    description: 'Откройте приложение WhatsApp и нажмите на иконку Настроек в нижней части экрана. Выберите пункт «Связанные устройства» — «Привязка устройства». ',
   // actions: action('link'),
  //  orientation: 'horizontal' as const
  },
  {
    label: 'Android',
    description: 'Откройте приложение WhatsApp, нажмите на три точки в правом верхнем углу экрана — «Связанные устройства» — «Связывание устройства». \n',
   // actions: action('primary'),
   // orientation: 'horizontal' as const
  },
  {
    label: 'Поделиться',
    description: 'Можно отправить сотруднику QR-код, чтобы он подключил свой телефон к линии.\n' +
        '\n' +
        'Для этого выбирите иконку, кликните на нее перейдите в открывшийся чат и отправьте ссылку ',
    //actions: action('link'),
   // orientation: 'horizontal' as const
  },

]
</script>

<template>
  <B24DescriptionList
      legend="Как отсканировать QR-код"
      text="краткая инструкция"
      :items="itemsActions"
  />
</template>