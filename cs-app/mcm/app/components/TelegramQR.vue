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
    label: '1',
    description: 'Запустить Telegram на основном устройстве (на смартфоне).' },
{
    label: '2',
    description: 'Открыть настройки: нажать на три горизонтальные полоски в левом верхнем углу экрана.' },
{
    label: '3',
    description: 'Перейти в раздел «Устройства» — здесь будет список всех устройств, на которых выполнен вход в аккаунт.'
      },
{
    label: '4',
    description:'Выбрать «Подключить устройство» — на экране появится QR-код, готовый к сканированию.'
      },
{
    label: '5',
    description: 'Открыть приложение Telegram на устройстве, которое нужно подключить. В настройках найти раздел «Устройства» и выбрать «Подключить устройство».'
      },
{
    label: '6',
    description: 'Навести камеру на QR-код, отображаемый на экране смартфона. Если камера не фокусируется, можно поднести устройство ближе или включить фонарь (если это возможно).'
      },
{
    label: '7',
    description: 'Подтвердить подключение: после сканирования кода на новом устройстве появится запрос на подтверждение подключения, нужно нажать «ОК», чтобы завершить процесс.',
  },

  {
    label: 'Поделиться',
    description: 'Можно отправить сотруднику QR-код, чтобы он подключил свой телефон к линии.\n' +
        '\n' +
        'Для этого выбирите иконку, кликните на нее перейдите в открывшийся чат и отправьте ссылку ',
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
