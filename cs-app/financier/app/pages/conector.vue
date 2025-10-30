<script setup lang="ts">
import { reactive, ref, useTemplateRef, nextTick, onMounted } from 'vue'
import * as z from 'zod'
import type { FormSubmitEvent } from '@bitrix24/b24ui-nuxt'
import SuccessIcon from '@bitrix24/b24icons-vue/button/SuccessIcon'
import Cross30Icon from '@bitrix24/b24icons-vue/actions/Cross30Icon'
import Shining2Icon from '@bitrix24/b24icons-vue/main/Shining2Icon'
import {
  getThumbnailSrc,
  formatDate,
  formatSubscriptionEndDate,
  authCheck,
} from '@/services/cs-main';
import { useNuxtApp } from '#app'
//import { useToast, useOverlay } from '#imports'
import { ProfileSliderAddProfile } from '#components'

const toast = useToast()
console.log(toast,'toast')
const overlay = useOverlay()
const profileSliderAddProfile = overlay.create(ProfileSliderAddProfile)

const { $initializeB24Frame } = useNuxtApp();
const $b24 = await $initializeB24Frame();
const authManager = $b24.auth;
const authData = authManager.getAuthData();
console.log(authData, 'authData')
console.log($b24.placement.options, 'placement.options')
defineOptions({ inheritAttrs: false })

// Реактивные переменные
const setupList = ref<SetupMessagerItem[]>([])
const profileList = ref<{ label: string; value: string }[]>([])
const hasActiveProfiles = ref(false)
const statusActiveProfiles = ref(false)
const itemProfileId = ref(false)
const itemProfile = ref(false)
const schema = z.object({
  selectMenu: z.object({
    value: z.string(),
    label: z.string(),
    id: z.string(), // добавляем id
  }).refine(item => item.value !== '', {
    message: 'Выберите профиль для подключения'
  })
})

type Schema = z.input<typeof schema>

const state = reactive<Partial<Schema>>({
  selectMenu: null,
})

const form = useTemplateRef('form')
const connector = $b24.placement.options.CONNECTOR === 'cs_mcm_whatsapp' ? 'Whatsapp' : 'Telegram'
// Функция для получения и обновления списка профилей
async function refreshProfiles() {
  const setupMesGet = await $b24.callMethod(
      'entity.item.get',
      {
        entity: 'setup_messager',
        filter: {ACTIVE: 'Y', PROPERTY_CS_TYPE: connector}
      }
  );

  const setup = setupMesGet.getData().result as SetupMessagerItem[];
  console.log(setup);
//
  // Проверяем, есть ли активный профиль с нужными параметрами
  const activeProfile = setup.find(item => {
    return (
        item.PROPERTY_VALUES.CS_LINE === $b24.placement.options.LINE &&
        item.PROPERTY_VALUES.CS_CONNECTOR === $b24.placement.options.CONNECTOR
    )
  })

  if (activeProfile) {
    console.log(activeProfile,'activeProfile')
    statusActiveProfiles.value = true
    itemProfileId.value = activeProfile.ID
    itemProfile.value = activeProfile.PROPERTY_VALUES.CS_PROFILE_ID

  } else {
    statusActiveProfiles.value = false

    profileList.value = setup
        .filter(item => !item.PROPERTY_VALUES.CS_LINE || item.PROPERTY_VALUES.CS_LINE === '')
        .map(item => ({
          label: item.NAME,
          value: item.PROPERTY_VALUES.CS_PROFILE_ID,
          id: item.ID // добавляем id для дальнейшей обработки
        }))
    console.log(profileList.value, 'profileList')
    hasActiveProfiles.value = profileList.value.length > 0
    console.log(hasActiveProfiles.value, 'hasActiveProfiles')
  }
}
async function handleConnectProfile() {
  const result = schema.safeParse(state)

  if (!result.success) {
    toast.error(result.error.errors[0].message)
    return
  }

  const selectedProfile = result.data.selectMenu
  console.log('Выбранный профиль:', selectedProfile)

  if (selectedProfile && selectedProfile.id) {
    console.log('ID выбранного профиля:', selectedProfile.id)

    // Шаг 1: Вызов imConnectorActivate
    const imConnectorActivate = await $b24.callMethod('imconnector.activate', {
      CONNECTOR: $b24.placement.options.CONNECTOR,
      LINE: $b24.placement.options.LINE,
      ACTIVE: 1,
    })

    console.log(imConnectorActivate, 'imConnectorActivate')
    const resImConnectorActivate = await imConnectorActivate.getData().result

    if (resImConnectorActivate === true) {
      // Шаг 2: Обновление сущности setup_messager
      const setupItemUpdate = await $b24.callMethod('entity.item.update', {
        ENTITY: 'setup_messager',
        ID: selectedProfile.id,
        PROPERTY_VALUES: {
          CS_LINE: $b24.placement.options.LINE,
          CS_CONNECTOR: $b24.placement.options.CONNECTOR,
          CS_STATUS: true,
        },
      })

      console.log(setupItemUpdate, 'setupItemUpdate')
      const resSetupItemUpdate = await setupItemUpdate.getData().result

      if (resSetupItemUpdate === true) {
        // Шаг 3: Вызов внешнего API для обновления профиля
        const updateProfile = await fetch('https://app.cassoft.ru/local/CSlibs/classes/app/mcm/functionVue.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            CONNECTOR: $b24.placement.options.CONNECTOR,
            LINE: $b24.placement.options.LINE,
            ACTIVE_STATUS: true,
            profile: selectedProfile.value,
            fn: 'profileUpdate',
          }),
        })
        console.log(updateProfile, 'updateProfile')
        const resUpdateProfile = await updateProfile.text()
        console.log(resUpdateProfile, 'resUpdateProfile')

        if (resUpdateProfile === 'Y') {
          // Шаг 4: Обновление списка профилей и изменение размера окна
          await refreshProfiles()
          resizeWindow()
         // toast.success('Профиль успешно подключен')
        } else {
         // toast.error('Ошибка при обновлении профиля')
        }
      } else {
       // toast.error('Ошибка при обновлении сущности')
      }
    } else {
     // toast.error('Ошибка при активации коннектора')
    }
  }
}

// Функция для изменения размера окна Bitrix24
function resizeWindow() {
  const script = document.createElement('script');
  script.src = 'https://api.bitrix24.com/api/v1/';
  script.onload = () => {
    if (typeof BX24 !== 'undefined' && typeof BX24.resizeWindow === 'function') {
      nextTick(() => {
        const { scrollWidth, scrollHeight } = BX24.getScrollSize();
        BX24.resizeWindow(scrollWidth, scrollHeight + 100);
      });
    } else {
      console.error('Метод BX24.resizeWindow не найден');
    }
  };
  script.onerror = () => {
    console.error('Ошибка загрузки скрипта BX24');
  };
  document.head.appendChild(script);
}

// Функция для открытия слайдера создания профиля
async function makeProfileAdd(): Promise<void> {
  console.log(connector, 'connector')
  await profileSliderAddProfile.open({ connector, onRefresh: refreshProfiles })
}
async function makeProfileClose(): Promise<void> {
  console.log($b24.placement.options, 'options')
  if (itemProfileId && itemProfile) {
console.log(itemProfileId,'itemProfileId')
console.log(itemProfile,'itemProfile')
    const imConnectorDeactivate = await $b24.callMethod('imconnector.activate', {
      CONNECTOR: $b24.placement.options.CONNECTOR,
      LINE: $b24.placement.options.LINE,
      ACTIVE: 0,
    })

    console.log(imConnectorDeactivate, 'imConnectorActivate')
    const resImConnectorDeactivate = await imConnectorDeactivate.getData().result

    if (resImConnectorDeactivate === true) {
      // Шаг 2: Обновление сущности setup_messager
      const setupItemUpdate = await $b24.callMethod('entity.item.update', {
        ENTITY: 'setup_messager',
        ID: itemProfileId.value,
        PROPERTY_VALUES: {
          CS_LINE: false,
          CS_CONNECTOR: false,
          CS_STATUS: false,
        },
      })

      console.log(setupItemUpdate, 'setupItemUpdate')
      const resSetupItemUpdate = await setupItemUpdate.getData().result

      if (resSetupItemUpdate === true) {
        // Шаг 3: Вызов внешнего API для обновления профиля
        const updateProfile = await fetch('https://app.cassoft.ru/local/CSlibs/classes/app/mcm/functionVue.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            CONNECTOR: false,
            LINE: false,
            ACTIVE_STATUS: false,
            profile: itemProfile.value,
            fn: 'profileUpdate',
          }),
        })
        console.log(updateProfile, 'updateProfile')
        const resUpdateProfile = await updateProfile.text()
        console.log(resUpdateProfile, 'resUpdateProfile')

        if (resUpdateProfile === 'Y') {
          // Шаг 4: Обновление списка профилей и изменение размера окна
          await refreshProfiles()
          resizeWindow()
         // toast.success('Профиль успешно отключен')
        } else {
        //  toast.error('Ошибка при обновлении профиля')
        }
      } else {
       // toast.error('Ошибка при обновлении сущности')
      }
    } else {
     // toast.error('Ошибка при деактивации коннектора')
    }
  }
}

onMounted(() => {
  refreshProfiles()
  resizeWindow()
})
</script>

<template>
  <B24Container class="mt-10"
                v-if="statusActiveProfiles">
    <div class="w-full flex flex-col items-center justify-center">
      <p class="text-h2 mb-6">Отключение профиля</p>
      <div class="w-full flex flex-row gap-1 items-center justify-center">
        <B24Button
            label="Отключить"
            size="lg"
            color="primary"
            loading-auto
            @click.stop="async () => { return makeProfileClose() }"
        />
      </div>
    </div>
  </B24Container>
  <B24Container v-else>
    <div class="mt-10">
      <p class="text-h2 mb-6">Подключение профиля</p>

      <!-- Если нет активных профилей -->
      <div v-if="!hasActiveProfiles && !statusActiveProfiles">
        <B24Advice
            class="min-w-full"
            :avatar="{ src: '/imges/avatar/assistant.png' }"
        >
          <p class="text-h2">У Вас нет свободных профилей для подключения</p>
          <p>Создайте новый профиль и потом подключите его к открытой линии</p>
        </B24Advice>

        <B24Container class="mt-12">
          <div class="w-full flex flex-col items-center justify-center">
            <p class="text-h2 mb-6">Создание нового профиля</p>
            <div class="w-full flex flex-row gap-1 items-center justify-center">
              <B24Button
                  label="Создать"
                  size="lg"
                  color="primary"
                  loading-auto
                  @click.stop="async () => { return makeProfileAdd() }"
              />
            </div>
          </div>
        </B24Container>
      </div>

      <!-- Если есть активные профили -->
      <B24Form
          v-if="hasActiveProfiles"
          v-bind="$attrs"
          ref="form"
          :state="state"
          :schema="schema"
          class="space-y-6"
      >
        <B24FormField name="selectMenu" label="Выберите профиль">
          <B24SelectMenu
              v-model="state.selectMenu"
              :items="profileList"
              class="w-full"
              :key="profileList.length"

          />
        </B24FormField>

        <B24Separator class="mt-6 mb-3" />

        <div class="flex flex-row gap-4 items-center justify-between">
          <B24Button
              type="submit"
              label="Подключить"
              color="success"
              @click="handleConnectProfile"
          />
        </div>
      </B24Form>

    </div>
  </B24Container>
</template>