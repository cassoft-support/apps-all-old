<script setup lang="ts">
import { ref, onMounted, computed, onUnmounted } from 'vue'
import { initializeB24Frame, B24Frame } from '@bitrix24/b24jssdk'
import type { ButtonProps } from '@bitrix24/b24ui-nuxt/types/index.ts'
import { formatDate } from '@/tools/cs-main'
import {  authorizationHH, refreshTokenHH, getHhApi } from '@/services/cs_hh'
import Refresh5Icon from '@bitrix24/b24icons-vue/actions/Refresh5Icon'
import { useToast, useOverlay } from '#imports'
import { SliderHandlerHH, SliderUsersHH } from '#components'
const overlay = useOverlay()
const sliderHandlerHH = overlay.create(SliderHandlerHH)
const sliderUsersHH = overlay.create(SliderUsersHH)

async function editHandlerHH(userId: string, userSettings: any): Promise<void> {
  await sliderHandlerHH.open({ activity: { userId, userSettings } })
}
async function editUsersHH(userId: string, ): Promise<void> {
  await sliderUsersHH.open({ userId })
}

definePageMeta({
  layout: 'menu'
})

let $b24: B24Frame | null = null
const setting = ref<Record<string, any>>({})
const settingID = ref<string | null>(null)
const settingProp = ref<Record<string, string>>({})
const currentUser = ref(null)
const timer = ref<string>('')

// Таймер
let intervalId: number | null = null

// Авторизация в HH
const authorizeInHH = async () => {
  if (!$b24) return;
  const authManager = $b24.auth;
  const authData = authManager.getAuthData();
  const userId = currentUser.value?.ID;

  try {
    const resAuthorizationHH = authorizationHH(authData.member_id, userId)
  } catch (error) {
    console.error('Ошибка получения client_id:', error);
  }
}

// Обновление токена
const refreshToken = async () => {
  const userId = currentUser.value?.ID;
  if (!userId || !setting.value[userId]) {
    console.warn('Пользователь не авторизован');
    return;
  }

  try {
    const response = refreshTokenHH(userId, setting.value[userId].refresh_token, setting.value[userId].access_token)
    console.log(response,'response')
    const data = await response;
   // const data = await response.json();
    if (data.access_token) {
      await saveAuthData(userId, {
        access_token: data.access_token,
        refresh_token: data.refresh_token,
        date_close: Math.floor((Date.now() + data.expires_in * 1000) / 1000)
      });
      await getSetting();
    } else {
      console.error('Ошибка обновления токена:', data);
    }
  } catch (error) {
    console.error('Ошибка при обновлении токена:', error);
  }
}

// Сохранение данных авторизации
const saveAuthData = async (userId: string, data: any) => {
  if (!$b24) return

  setting.value[userId] = data

  const propertyValues = {
    CS_HH_KEY: JSON.stringify(setting.value)
  }

  try {
    await $b24.callMethod('entity.item.update', {
      ENTITY: 'setup',
      ID: settingID.value,
      DATA: {
        PROPERTY_VALUES: propertyValues
      }
    })

    console.log('Данные пользователя', userId, 'обновлены')
  } catch (error) {
    console.error('Ошибка сохранения данных:', error)
  }
}

// Получение данных
async function getSetting() {
  if (!$b24) {
    console.error('B24Frame не инициализирован')
    return
  }

  try {
    const resProp = await $b24.callMethod('entity.item.property.get', { ENTITY: 'setup' })
    const dataProp = resProp.getData().result

    if (Array.isArray(dataProp)) {
      settingProp.value = dataProp.reduce((acc, item) => {
        acc[item.PROPERTY] = item.NAME
        return acc
      }, {} as Record<string, string>)
    } else {
      console.warn('Ожидался массив, но пришёл объект:', dataProp)
      settingProp.value = {}
    }

    const response = await $b24.callBatch({
      result: {
        method: 'entity.item.get',
        params: {
          ENTITY: 'setup',
          PARAMS: {
            filter: { ACTIVE: 'Y' },
            select: ['ID', 'NAME', 'PROPERTY_VALUES']
          }
        }
      }
    }, true)

    const data = response.getData().result[0]

    if (!data) {
      const newItem = await $b24.callMethod('entity.item.add', {
        ENTITY: 'setup',
        DATA: {
          NAME: 'Настройки профиля',
          ACTIVE: 'Y',
          PROPERTY_VALUES: {}
        }
      })

      const newItemData = newItem.getData().result
      settingID.value = newItemData.ID
      console.log('Создана новая запись:', newItemData)
    } else {
      settingID.value = data.ID
      console.log('Используем существующую запись:', data)
    }

    const itemResponse = await $b24.callMethod('entity.item.get', {
      ENTITY: 'setup',
      PARAMS: {
        filter: { ID: settingID.value },
        select: ['PROPERTY_VALUES']
      }
    })

    const itemData = itemResponse.getData().result[0]

    const hhKey = itemData?.PROPERTY_VALUES?.CS_HH_KEY

    if (hhKey) {
      try {
        setting.value = JSON.parse(hhKey)
      } catch (e) {
        console.warn('CS_HH_KEY не является JSON:', hhKey)
        setting.value = hhKey
      }
    } else {
      setting.value = {}
    }
    console.log(setting.value,'itemData')
  } catch (error) {
    console.error('Ошибка получения данных:', error)
  }
}

// Проверка авторизации
function hasUserAuth(userId: string): boolean {
  return Boolean(setting.value[userId])
}

// Форматирование даты закрытия
const formatDateClose = computed(() => {
  const userId = currentUser.value?.ID
  if (!userId || !setting.value[userId]) return ''

  const dateClose = setting.value[userId].date_close
  return formatDate(dateClose)
})

export interface ExampleProps {
  text?: string
}

const propsUpdate = withDefaults(defineProps<ExampleProps>(), {
  text: 'Обновить'
})

// Получение списка подписок пользователя
const getSubscriptions = async () => {
  const userId = currentUser.value?.ID
  if (!userId || !setting.value[userId]) {
    console.warn('Пользователь не авторизован')
    return
  }

  const accessToken = setting.value[userId].access_token

  try {
    const subscriptions = await getHhApi('/webhook/subscriptions', accessToken)
    console.log('Список подписок пользователя:', subscriptions)
  } catch (error) {
    console.error('Ошибка получения подписок:', error)
  }
}

// Функция для GET-запроса к API HH.ru


onMounted(async () => {
  try {
    $b24 = await initializeB24Frame()
    const resUser = await $b24.callMethod('user.current')
    const user = resUser.getData().result

    currentUser.value = user
    const resAdmin = await $b24.callMethod('user.admin');
    const isAdmin = resAdmin.getData().result;
    await getSetting()
// startTimer()
  } catch (error) {
    console.error('Ошибка инициализации:', error)
  }
})

onUnmounted(() => {
  if (intervalId) clearInterval(intervalId)
})
</script>

<template>
  <!-- <div class=""><pre>{{setting}}</pre></div>-->
  <div class="mb-4"><h1>Настройки</h1></div>
  <B24Container >
    <div class="bg-white flex flex-col w-full rounded-2xl px-10">
      <div class="flex flex-row justify-center m-10">
        <div class="w-[40px] h-[40px] rounded-circle overflow-hidden">
          <img class="object-cover" src="https://app.cassoft.ru/images/logo_app/min-hh-red.png" alt="">
        </div>
      </div>

      <div v-if="currentUser" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Заголовки -->
        <div class="font-semibold text-sm">Пользователь</div>
        <div class="font-semibold text-sm">Дата окончания подключения</div>
        <div class="font-semibold text-sm">Подписка на событие</div>

        <!-- Данные -->
        <div v-if="hasUserAuth(currentUser.ID)" class="flex flex-col sm:flex-row items-center sm:items-start">
          <div class="flex items-center mb-4 sm:mb-0 p-4 mb-2">
            <B24Avatar
                :src="currentUser.PERSONAL_PHOTO || 'https://app.cassoft.ru/cs-app/cs-core/content/images/avatar/no-avatar.jpg'"
                :alt="currentUser.NAME + ' ' + currentUser.LAST_NAME"
                size="lg"
            />
            <div class="ml-3">
              <div class="font-medium">{{ currentUser.NAME }} {{ currentUser.LAST_NAME }}</div>
            </div>
          </div>
        </div>

        <div v-if="hasUserAuth(currentUser.ID)" class="flex items-center p-4 mb-2">
          <span class="mr-4">{{ formatDateClose }}</span>
          <B24Tooltip :text="propsUpdate.text">
        <span
            class="cursor-pointer transition-transform duration-200 hover:scale-110 active:scale-95"
            @click="refreshToken"
        >
         <Refresh5Icon class="w-7 h-7" />
        </span>
          </B24Tooltip>
        </div>

        <div v-if="hasUserAuth(currentUser.ID)" class="flex items-center p-4 mb-2">
          <B24Button
              color="primary"
              depth="normal"
              @click="editHandlerHH(currentUser.ID, setting[currentUser.ID])"
          >
            Подписаться
          </B24Button>
          <B24Button
              color="secondary"
              depth="normal"
              class="ml-4"
              @click="getSubscriptions"
          >
            Получить подписки
          </B24Button>
        </div>

        <!-- Если не авторизован -->
        <div  class="col-span-3 flex items-center justify-center">
          <B24Button
              :active="true"
              :b24ui="{ baseLine: 'justify-center w-[200px]' }"
              color="success"
              depth="normal"
              active-color="primary"
              active-depth="normal"
              @click="authorizeInHH"
          >
            Авторизоваться в HH
          </B24Button>
        </div>
        <div  class="col-span-3 flex items-center justify-center">
        <B24Button
            color="primary"
            depth="normal"
            @click="editUsersHH(currentUser.ID)"
        >
          Сопоставить пользователей
        </B24Button>
        </div>
      </div>

    </div>
  </B24Container>
</template>
