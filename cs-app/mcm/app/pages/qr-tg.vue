<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useRoute } from 'vue-router'
import { B24Icon } from '@bitrix24/b24icons-vue'
import QrCode2Icon from '@bitrix24/b24icons-vue/main/QrCode2Icon'
import Barcode1Icon from '@bitrix24/b24icons-vue/main/Barcode1Icon'
import { getThumbnailSrc } from '@/tools/cs-main';
import type { DescriptionListItem } from '@bitrix24/b24ui-nuxt/components/DescriptionList.vue'
const route = useRoute()
import { wappGet, wappPost } from '@/services/cs-wappi';

// Получаем profile_id и profile_name из query параметров
const profileId = ref<string | null>(null)
const profileName = ref<string | null>(null)

// Инициализируем значения из URL
profileId.value = route.query.profile_id as string
profileName.value = route.query.profile_name as string
console.log(route,'route')
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
const isCodeStage = ref(false)
const codeInput = ref('')
const authError = ref(false)
const codeExpired = ref(false)
const is2faStage = ref(false)
const pwdCode = ref('')

async function processQRCode(profileId: string): Promise<any> {
  try {
    const urlQR = `/tapi/sync/auth/qr?profile_id=${profileId}`;
    console.log(urlQR,'urlQR')
    const result = await wappGet(urlQR);
console.log(result,'result')
    if (result.status === 'done' && result.detail !== 'auth_success result') {
        qrCodeSrc.value = getThumbnailSrc(result.detail)
        console.log(qrCodeSrc,'qrCodeSrc')


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
  try {
    let urlCode = '';
    let body = '';
    if (profileId ) {
      body = JSON.stringify({ phone: phone });
      urlCode = `/tapi/sync/auth/phone?profile_id=${profileId}`;
    } else {
      console.warn('Profile is undefined');
    }

    const resultCode = await wappPost(urlCode, body);
    if (resultCode.status === 'done') {
      // ...
    } else if (resultCode.status !== 'done' && resultCode.detail === 'You are already authorized') {
      resultActive.value = true
      props.onRefresh?.()
    }
  } catch (error) {
    console.log('error', error)
  }
}
function handleSubmit() {
  makeProfileCode(state.value.phone)
  isCodeStage.value = true
}

onMounted(() => {
  if (profileId.value) {
    processQRCode(profileId.value)
  }
})

onBeforeUnmount(() => {
  shouldUpdate.value = false
})
async function sendAuthCode() {
  try {
    let urlCode = '';
    let body = '';
    if (profileId) {
      body = JSON.stringify({ auth_code: codeInput.value });
      urlCode = `/tapi/sync/auth/code?profile_id=${props.profile.profile}`;
    } else {
      console.warn('Profile is undefined');
    }

    const resultCode = await wappPost(urlCode, body);
    if (resultCode.detail === 'auth_success') {
      if (resultCode.detail === '2fa') {
        is2faStage.value = true
      } else {
        resultActive.value = true
        props.onRefresh?.()
      }
    } else if (resultCode.status === 'error' && resultCode.detail === 'auth_error') {
      authError.value = true
      setTimeout(() => {
        authError.value = false
      }, 2000)
    } else if (resultCode.status === 'error' && resultCode.detail === 'Timeout exception') {
      codeExpired.value = true
      setTimeout(() => {
        codeExpired.value = false
      }, 2000)
      makeProfileCode(state.value.phone)
    } else {
      console.error('Ошибка при отправке кода:', resultCode)
    }
  } catch (error) {
    console.log('error', error)
  }
}

async function send2faPassword() {
  try {
    let url2fa = '';
    let body = '';
    if (props.profile && props.profile.type === 'Telegram') {
      body = JSON.stringify({ pwd_code: pwdCode.value });
      url2fa = `/tapi/sync/auth/2fa?profile_id=${props.profile.profile}`;
    } else {
      console.warn('Profile is undefined');
    }

    const result2fa = await wappPost(url2fa, body);
    if (result2fa.status === 'done') {
      resultActive.value = true
    //  props.onRefresh?.()
    } else {
      console.error('Ошибка при отправке пароля 2fa:', result2fa)
    }
  } catch (error) {
    console.log('error', error)
  }
}

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
      <B24Tabs :items="items" variant="link" class="gap-4 w-full" :ui="{ trigger: 'flex-1' }">
        <template #QRactive="{ item }">
          <div class="flex flex-col items-center justify-center">
            <p class="text-base-500 dark:text-base-400 mt-4 text-md">
              {{ item.description }}
            </p>
            <img ref="qrCodeImage" :src="qrCodeSrc" />

          </div>
          <TelegramQR />
        </template>

        <template #codeActive="{ item }">
          <div ref="codePhone" class="text-h2"></div>
          <p class="text-base-500 dark:text-base-400 mb-4 text-md">
            {{ item.description }}
          </p>

          <!-- Форма для ввода телефона -->
          <B24Form v-if="!isCodeStage" :state="state" class="flex flex-col gap-4" @submit.prevent="handleSubmit">
            <B24FormField label="Введите номер телефона" name="phone">
              <B24Input v-model="state.phone" class="w-full" />
            </B24FormField>
            <B24Button label="Отправить" type="submit" color="success" class="self-end" />
          </B24Form>

          <!-- Форма для ввода кода -->
          <B24Form v-else-if="!is2faStage" :state="{ code: codeInput }" class="flex flex-col gap-4" @submit.prevent="sendAuthCode">
            <B24FormField label="Введите полученный код" name="code">
              <B24Input v-model="codeInput" class="w-full" />
            </B24FormField>
            <B24Button label="Отправить код" type="submit" color="success" class="self-end" />
          </B24Form>

          <!-- Форма для ввода пароля 2fa -->
          <B24Form v-else :state="{ pwd: pwdCode }" class="flex flex-col gap-4" @submit.prevent="send2faPassword">
            <B24FormField label="Введите пароль от двухфакторной авторизации" name="pwd">
              <B24Input v-model="pwdCode" class="w-full" />
            </B24FormField>
            <B24Button label="Отправить пароль" type="submit" color="success" class="self-end" />
          </B24Form>

          <!-- Сообщение об ошибке кода -->
          <div v-if="authError" class="mt-4 text-red-500">
            <p>Ошибка: неверный код. Повторите попытку.</p>
          </div>

          <!-- Сообщение о истечении срока кода -->
          <div v-if="codeExpired" class="mt-4 text-red-500">
            <p>Код устарел. Новый код был отправлен на ваш телефон.</p>
          </div>
        </template>
      </B24Tabs>
    </div>
  </div>
  </B24Container>
  </div>
</template>