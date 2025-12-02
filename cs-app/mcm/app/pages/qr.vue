<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useRoute } from 'vue-router'
import { B24Icon } from '@bitrix24/b24icons-vue'
import QrCode2Icon from '@bitrix24/b24icons-vue/main/QrCode2Icon'
import Barcode1Icon from '@bitrix24/b24icons-vue/main/Barcode1Icon'

import type { DescriptionListItem } from '@bitrix24/b24ui-nuxt/components/DescriptionList.vue'
const route = useRoute()
import { wappGet } from '@/services/cs-wappi';

// Получаем profile_id и profile_name из query параметров
const profileId = ref<string | null>(null)
const profileName = ref<string | null>(null)

// Инициализируем значения из URL
profileId.value = route.query.profile_id as string
profileName.value = route.query.profile_name as string

const items = [
  {
    label: 'Активация QR-code',
    description: 'Отсканируйте код на вашем смартфоне.',
    icon: QrCode2Icon,
    slot: 'QRactive'
  },
  {
    label: 'Активация через код',
    description: 'Введите номер телефона и получите код активации',
    icon: Barcode1Icon,
    slot: 'codeActive'
  },
]

const qrCodeSrc = ref('')
const resultActive = ref(false)
const shouldUpdate = ref(true)


async function processQRCode(profileId: string): Promise<any> {
  try {
    const urlQR = `/api/sync/qr/get?profile_id=${profileId}`;
    const result = await wappGet(urlQR);

    if (result.status === 'done') {
      if (result.qrCode) {
        qrCodeSrc.value = result.qrCode
      }

      if (shouldUpdate.value) {
        setTimeout(() => {
          processQRCode(profileId)
        }, 20000)
      }
    } else if (result.status !== 'done' && result.detail === 'You are already authorized') {
      resultActive.value = true
    }

    return result
  } catch (error) {
    console.error('Ошибка при обработке QR-кода:', error)
    throw error
  }
}

const state = ref({
  phone: ''
})

const codePhone = ref(null)

async function makeProfileCode(phone: string) {
  const myHeaders = new Headers()
  myHeaders.append("Authorization", `${token}`)

  const requestOptions = {
    method: 'GET',
    headers: myHeaders,
    redirect: 'follow'
  }

  try {
    const urlCode = `/api/sync/auth/code?profile_id=${props.profile.profile}&phone=${phone}`
    const result = await wappGet(urlCode);

    if (result.status === 'done') {
      if (codePhone.value) {
        codePhone.value.innerText = result.code
      }

      if (shouldUpdate.value) {
        setTimeout(async () => {
          await makeProfileCode(phone)
        }, 160000)
      }
    } else if (result.status !== 'done' && result.detail === 'You are already authorized') {
      resultActive.value = true
    }
  } catch (error) {
    console.log('error', error)
  }
}

onMounted(() => {
  if (profileId.value) {
    processQRCode(profileId.value)
  }
})

onBeforeUnmount(() => {
  shouldUpdate.value = false
})
const itemsActions: DescriptionListItem[] = [
  {
    label: 'iOS',
    description: 'Откройте приложение WhatsApp и нажмите на иконку Настроек в нижней части экрана. Выберите пункт «Связанные устройства» — «Привязка устройства». ',
  },
  {
    label: 'Android',
    description: 'Откройте приложение WhatsApp, нажмите на три точки в правом верхнем углу экрана — «Связанные устройства» — «Связывание устройства». \n',
  },

]
</script>
<template >
  <div class="bg-cyan-300 h-[100vh] p-5">
  <B24Container class="bg-white rounded-md">
  <div class="p-6">
    <!-- Заголовок страницы -->
    <h1 class="text-xl font-bold mb-4">
      Активация профиля: {{ profileName || 'Не указано' }}
    </h1>

    <div v-if="resultActive" class="mb-4 flex flex-wrap items-center justify-start gap-4">
      <B24Advice
          class="min-w-full"
          :avatar="{ src: '/imges/avatar/assistant.png' }"
      >
        <p class="text-h2">Активация завершена</p>
        <p>Вы уже авторизованы</p>
      </B24Advice>
    </div>

    <div v-else>
      <B24Tabs
          :items="items"
          variant="link"
          class="gap-4 w-full"
          :ui="{ trigger: 'flex-1' }"
      >
        <template #QRactive="{ item }">
          <div class="flex items-center justify-center mt-4 mb-5">
            <p class="text-base-500 dark:text-base-400  text-md">
              {{ item.description }}
            </p>
          </div>
          <div class="flex flex-col items-center justify-center">
            <img ref="qrCodeImage" :src="qrCodeSrc" />
          </div>

          <div class="">
          <B24DescriptionList
              legend="Как отсканировать QR-код"
              text="краткая инструкция"
              :items="itemsActions"
          />
          </div>

        </template>

        <template #codeActive="{ item }">
          <div ref="codePhone" class="text-h2"></div>
          <p class="text-base-500 dark:text-base-400 mb-4 text-md">
            {{ item.description }}
          </p>
          <B24Form :state="state" class="flex flex-col gap-4" @submit.prevent="makeProfileCode(state.phone)">
            <B24FormField label="Введите номер телефона" name="phone">
              <B24Input v-model="state.phone" class="w-full" />
            </B24FormField>
            <B24Button label="Отправить" type="submit" color="success" class="self-end" />
          </B24Form>
        </template>
      </B24Tabs>
    </div>
  </div>
  </B24Container>
  </div>
</template>