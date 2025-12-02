<script setup lang="ts">
import type { Collections } from '@nuxt/content'
import { useNuxtApp } from '#app'
import { reactive, ref, computed, onMounted, defineProps, onBeforeUnmount, nextTick } from 'vue'
import Barcode1Icon from '@bitrix24/b24icons-vue/main/Barcode1Icon'
import QrCode2Icon from '@bitrix24/b24icons-vue/main/QrCode2Icon'
import { B24Icon } from '@bitrix24/b24icons-vue'
import { WhatsAppQR } from '#components'
import { useOverlay } from '#imports'
import { defineEmits } from 'vue'
import { wappGet } from '@/services/cs-wappi'
import { ProfilesWappiAdd } from '@/services/cs-profile'

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

const props = defineProps<{
  profile: any
  onRefresh?: () => void
}>()

const emit = defineEmits<{ close: [boolean] }>()
const qrCodeSrc = ref<string>('/images/scanning-qr-code.jpg')
const shouldUpdate = ref<boolean>(true)
const resultActive = ref<boolean>(false)
const state = ref({
  phone: ''
})
const codePhone = ref(null)

// Основная логика активации
async function processQRCode(profile: any): Promise<any> {
  try {
    let urlQR = '';
    if (profile && profile.type === 'Whatsapp') {
      urlQR = `/api/sync/qr/get?profile_id=${profile.profile}`;
    } else {
      console.warn('Profile is undefined');
      return;
    }

    console.log('urlQR:', urlQR)
    const result = await wappGet(urlQR);

    if (result.status === 'done') {
      console.log('result.qrCode:', result.qrCode)
      if (result.qrCode) {
        qrCodeSrc.value = result.qrCode
      }

      if (shouldUpdate.value) {
        setTimeout(() => {
          processQRCode(profile)
        }, 20000)
      }
    } else if (result.status !== 'done' && result.detail === 'You are already authorized') {
      resultActive.value = true
      props.onRefresh?.()
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
    if (props.profile && props.profile.type === 'Whatsapp') {
      urlCode = `/api/sync/auth/code?profile_id=${props.profile.profile}&phone=${phone}`;
    } else {
      console.warn('Profile is undefined');
      return;
    }

    console.log('urlCode:', urlCode)
    const result = await wappGet(urlCode);
    console.log('result:', result)

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
      props.onRefresh?.()
    }
  } catch (error) {
    console.log('error', error)
  }
}

const showCopied = ref(false)

function handleSubmit() {
  makeProfileCode(state.value.phone)
}

const qrLink = computed(() => {
  if (!props.profile || !props.profile.profile || !props.profile.name) {
    console.log('qrLink: props.profile не содержит нужных данных')
    return ''
  }
  return `https://app.cassoft.ru/cs-app/mcm/qr?profile_id=${props.profile.profile}&profile_name=${props.profile.name}`
})

const shareButtonHtml = computed(() => {
  console.log('shareButtonHtml-start')
  console.log('qrLink:', qrLink.value)

  const html = `
<div class="ya-share2"
     data-curtain
     data-shape="round"
     data-url="${qrLink.value}"
     data-title="QR-активация"
     data-description="Активация через QR-код"
     data-services="vkontakte,odnoklassniki,telegram,viber,whatsapp">
</div>
`

  console.log('shareButtonHtml:', html)
  return html
})

onMounted(async () => {
  if (props.profile && props.profile.profile && props.profile.resourceProfile !== false) {
    console.log(props.profile.resourceProfile,'onMounted: запускаем processQRCode')
    processQRCode(props.profile)

    const script = document.createElement('script');
    script.src = 'https://yastatic.net/share2/share.js';
    script.async = true;
    document.head.appendChild(script);
  }

// Проверка на создание профиля
  if (props.profile && props.profile.resourceProfile === false) {
    console.log('props.profile.resourceProfile === false')
      console.log('dateClose > nowTimestamp — запускаем создание профиля')
      const { $initializeB24Frame } = useNuxtApp()
      const $b24 = await $initializeB24Frame()
      const profileWappiNew = await ProfilesWappiAdd($b24, props.profile)
      console.log('profileWappiNew:', profileWappiNew)
      if (profileWappiNew) {
        props.profile.profile = profileWappiNew.profile_id
        props.profile.dateClose = profileWappiNew.date_close
        props.profile.resourceProfile = true
        console.log('props.profile обновлён:', props.profile)
        processQRCode(props.profile)
        const script = document.createElement('script');
        script.src = 'https://yastatic.net/share2/share.js';
        script.async = true;
        document.head.appendChild(script);
      }

  }
})

onBeforeUnmount(() => {
  shouldUpdate.value = false
})
</script>

<template>
  <B24Slideover
      :title="profile.profileName"
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
              :avatar="{ src: '/imges/avatar/assistant.png' }"
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
              <WhatsAppQR />
            </template>

            <template #codeActive="{ item }">
              <div ref="codePhone" class="text-h2"></div>
              <p class="text-base-500 dark:text-base-400 mb-4 text-md">
                {{ item.description }}
              </p>
              <B24Form :state="state" class="flex flex-col gap-4" @submit.prevent="handleSubmit">
                <B24FormField label="Введите номер телефона" name="phone">
                  <B24Input v-model="state.phone" class="w-full" />
                </B24FormField>
                <B24Button label="Отправить" type="submit" color="success" class="self-end" />
              </B24Form>
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