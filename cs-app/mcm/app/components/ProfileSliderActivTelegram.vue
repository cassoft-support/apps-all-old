<script setup lang="ts">
import type { Collections } from '@nuxt/content'
import { useNuxtApp } from '#app'
import { reactive, ref, computed, onMounted, defineProps, onBeforeUnmount, nextTick } from 'vue'
import Barcode1Icon from '@bitrix24/b24icons-vue/main/Barcode1Icon'
import QrCode2Icon from '@bitrix24/b24icons-vue/main/QrCode2Icon'
import { B24Icon } from '@bitrix24/b24icons-vue'
import { TelegramQR } from '#components'
import { useOverlay } from '#imports'
import { defineEmits } from 'vue'
import { wappGet, wappPost } from '@/services/cs-wappi';
import { ProfilesWappiAdd } from '@/services/cs-profile';
import { getThumbnailSrc } from '@/tools/cs-main';

const overlay = useOverlay()

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

const emit = defineEmits<{ close: [boolean] }>()
const qrCodeSrc = ref<string>('/images/scanning-qr-code.jpg')
const shouldUpdate = ref<boolean>(true)
const resultActive = ref<boolean>(false)
const state = ref({ phone: '' })
const codePhone = ref(null)
const props = defineProps<{
  profile: IActivity
  onRefresh?: () => void
}>()

// Новое состояние для отслеживания стадии активации
const isCodeStage = ref(false)
const codeInput = ref('')
const authError = ref(false)
const codeExpired = ref(false)
const is2faStage = ref(false)
const pwdCode = ref('')

// Обработка создания профиля
  if (props.profile.resourceProfile === false) {
    console.log(props.profile.dateClose, 'false')
    const dateCloseTimestamp = props.profile.dateClose * 1000;
    const nowTimestamp = new Date().getTime();
console.log(nowTimestamp,'nowTimestamp')
    if (dateCloseTimestamp > nowTimestamp) {
      console.log('date-close')
      const {$initializeB24Frame} = useNuxtApp()
      const $b24 = await $initializeB24Frame()
      const profileWappiNew = await ProfilesWappiAdd($b24, props.profile)
      console.log(profileWappiNew, 'profileWappiNew')
      if (profileWappiNew) {
        props.profile.profile = profileWappiNew.profile_id
        props.profile.dateClose = profileWappiNew.date_close
        props.profile.resourceProfile = true
      }
    }
  }

// Основная логика активации

const maxAttempts = 10
const attemptCount = ref<number>(0)

async function processQRCode(profile: IActivity): Promise<any> {
  try {
    let urlQR = '';
    if (profile && profile.type === 'Telegram') {
      urlQR = `/tapi/sync/auth/qr?profile_id=${profile.profile}`;
    } else {
      console.warn('Profile is undefined');
      return;
    }

    const result = await wappGet(urlQR);

    if (result.status === 'done' && result.detail !== 'auth_success result') {
      qrCodeSrc.value = getThumbnailSrc(result.detail)

      if (shouldUpdate.value && attemptCount.value < maxAttempts) {
        attemptCount.value += 1
        setTimeout(() => {
          processQRCode(profile)
        }, 20000)
      } else if (attemptCount.value >= maxAttempts) {
        alert('Количество попыток обновления закончилось. Для получения кода обновите страницу.')
        shouldUpdate.value = false
      }
    } else if (result.status !== 'done' && result.detail === 'auth_success result') {
      resultActive.value = true
      props.onRefresh?.()
      shouldUpdate.value = false
    }
    return result
  } catch (error) {
    console.error('Ошибка при обработке QR-кода:', error)
    throw error
  }
}

  async function makeProfileCode(phone: string) {
    try {
      let urlCode = '';
      let body = '';
      if (props.profile && props.profile.type === 'Telegram') {
        body = JSON.stringify({ phone: phone });
        urlCode = `/tapi/sync/auth/phone?profile_id=${props.profile.profile}`;
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

  const qrLink = computed(() => {
    return `https://app.cassoft.ru/cs-app/mcm/qr-tg?profile_id=${props.profile.profile}&profile_name=${props.profile.name}`
  })

  const shareButtonHtml = computed(() => {
    return `
    <div class="ya-share2"
         data-curtain
         data-shape="round"
         data-url="${qrLink.value}"
         data-title="QR-активация"
         data-description="Активация через QR-код"
         data-services="vkontakte,odnoklassniki,telegram,viber,whatsapp">
    </div>
`
  })

  onMounted(() => {
    if (props.profile && props.profile.profile && props.profile.resourceProfile !== false) {
      processQRCode(props.profile)
      const script = document.createElement('script')
      script.src = 'https://yastatic.net/share2/share.js'
      script.async = true
      document.head.appendChild(script)
    }
  })

  onBeforeUnmount(() => {
    shouldUpdate.value = false
  })

  async function sendAuthCode() {
    try {
      let urlCode = '';
      let body = '';
      if (props.profile && props.profile.type === 'Telegram') {
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
        props.onRefresh?.()
      } else {
        console.error('Ошибка при отправке пароля 2fa:', result2fa)
      }
    } catch (error) {
      console.log('error', error)
    }
  }

</script>
<template>
  <B24Slideover
      :title="profile.name"
      :description="props.profile.profileName"
      :close="{ onClick: () => { emit('close', false); shouldUpdate.value = false } }"
      :b24ui="{
     content: 'max-w-[90%] md:max-w-1/2',
     body: 'm-5 p-5 bg-white dark:bg-white/10 rounded'
    }"
  >
    <template #body>
      <div class="content-container">
        <div v-if="resultActive" class="mb-4 flex flex-wrap items-center justify-start gap-4">
          <B24Advice
              class="min-w-full"
              :avatar="{ src: '/images/avatar/assistant.png' }"
          >
            <p class="text-h2">{{ $t('component.profile.activation.resultActive.p1') }}</p>
            <p>{{ $t('component.profile.activation.resultActive.p2') }}</p>
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
                <a
                    :href="qrLink"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex items-center gap-2 text-blue-600 hover:underline cursor-pointer"
                >
                  <B24Icon name="Actions::ReplyIcon" class="w-4 h-4" />
                  <div class="text-sm">Отправить ссылку на QR-код</div>
                  <B24Icon name="Button::BtnIconCopyIcon" class="w-4 h-4" />
                </a>

                <div class="mt-4">
                  <div v-html="shareButtonHtml"></div>
                </div>
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
    </template>

    <template #footer>
      <div class="flex gap-2">
        <B24Button
            rounded
            :label="$t('component.activity.item.slider.close')"
            color="link"
            depth="dark"
            @click="emit('close', false)"
        />
      </div>
    </template>
  </B24Slideover>
</template>