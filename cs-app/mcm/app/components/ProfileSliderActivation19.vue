<script setup lang="ts">
import type { Collections } from '@nuxt/content'
const { locale, defaultLocale } = useI18n()
import { reactive, ref, computed, onMounted, defineProps, onBeforeUnmount } from 'vue'
import Barcode1Icon from '@bitrix24/b24icons-vue/main/Barcode1Icon'
import QrCode2Icon from '@bitrix24/b24icons-vue/main/QrCode2Icon'
import { B24Icon } from '@bitrix24/b24icons-vue'


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


var token = '785026ea43c1bb0b1b842189cbca9197c05f424e';

const props = defineProps<{
  profile: IActivity
}>();
const qrCodeImage = ref(null);
const resultActive = ref(false);

const qrCodeSrc = ref('');
const shouldUpdate = ref(true);
async function fetchQR(profileId: string): Promise<any> {
  const myHeaders = new Headers();
  myHeaders.append("Authorization", `${token}`);

  const requestOptions: RequestInit = {
    method: 'GET',
    headers: myHeaders,
    redirect: 'follow'
  };

  return fetch(`https://wappi.pro/api/sync/qr/get?profile_id=${profileId}`, requestOptions)
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .catch(error => {
        console.error('Ошибка при получении QR-кода:', error);
        throw error;
      });
}

async function processQRCode(profileId: string): Promise<any> {
  try {
    const result = await fetchQR(profileId);


    if (result.status === 'done') {
      console.log('Result:', result); // Обработка результата
      // Обновляем содержимое блока
      if ( result.qrCode) {
        //qrCodeImage.value.src = result.qrCode;
        qrCodeSrc.value = result.qrCode;
        console.log('QR-код обновлен:', result.qrCode);
      }

      if (shouldUpdate.value) {
        setTimeout(() => {
          processQRCode(profileId);
        }, 20000);
      }
    } else if(result.status !== 'done' && result.detail ==='You are already authorized'){
      resultActive.value = true;
    }
    return result; // Возвращаем результат для дальнейшего использования
  } catch (error) {
    console.error('Ошибка при обработке QR-кода:', error);
    throw error; // Пробрасываем ошибку, если необходимо
  }
}



const emit = defineEmits<{ close: [boolean] }>()
//
// // region Locale ////
const contentCollection = computed<keyof Collections>(() => `contentActivities_${locale.value.length > 0 ? locale.value : defaultLocale}`)
// // endregion ////

// const state = reactive({
// phone: 'Введите номер телефона',
// })
const state = ref({
  phone: ''
});
const codePhone = ref(null);
async function makeProfileCode(phone: string) {
  const myHeaders = new Headers();
  myHeaders.append("Authorization", `${token}`);

  const requestOptions = {
    method: 'GET',
    headers: myHeaders,
    redirect: 'follow'
  };

  try {
    const response = await fetch(`https://wappi.pro/api/sync/auth/code?profile_id=${props.profile.profile}&phone=${phone}`, requestOptions);
    const result = await response.json();;

    console.log(result);
    if(result.status === 'done') {
      // Обновляем содержимое блока
      if (codePhone.value) {
        codePhone.value.innerText = result.code;
      }
      if (shouldUpdate.value) {
        setTimeout(async () => {
          await makeProfileCode(phone);
        }, 160000);
      }
    }
    else if(result.status !== 'done' && result.detail ==='You are already authorized'){
      resultActive.value = true;
    }
  } catch (error) {
    console.log('error', error);
  }
}
const showCopied = ref(false);

function copyLinkToClipboard() {
  const link = `https://app.cassoft.ru/cs-app/mcm/app/pages/qr.vue?profile_id=${props.profile.profile}`;

// Попытка использовать современный API
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(link)
        .then(() => {
          console.log('Ссылка скопирована (через navigator.clipboard)');
          showCopied.value = true;
          setTimeout(() => {
            showCopied.value = false;
          }, 2000);
        })
        .catch(err => {
          console.error('Ошибка копирования через navigator.clipboard:', err);
          fallbackCopyTextToClipboard(link);
        });
  } else {
    fallbackCopyTextToClipboard(link);
  }
}

function fallbackCopyTextToClipboard(text: string) {
// Попытка через textarea
  const textArea = document.createElement('textarea');
  textArea.value = text;
  textArea.style.position = 'absolute';
  textArea.style.left = '-9999px';
  document.body.appendChild(textArea);
  textArea.focus();
  textArea.select();

  try {
    document.execCommand('copy');
    console.log('Ссылка скопирована (через textarea)');
    showCopied.value = true;
    setTimeout(() => {
      showCopied.value = false;
    }, 2000);
  } catch (err) {
    console.error('Ошибка копирования через textarea:', err);
    // Если всё не сработало — показываем prompt
    prompt('Скопируйте ссылку вручную:', text);
  } finally {
    document.body.removeChild(textArea);
  }
}
function handleSubmit() {
  makeProfileCode(state.value.phone);
}
const qrLink = computed(() => {
  return `https://app.cassoft.ru/cs-app/mcm/qr?profile_id=${props.profile.profile}`;
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
`;
});
onMounted(() => {
  if (props.profile && props.profile.profile) {
    console.log(props.profile.profile);

    processQRCode(props.profile.profile);
    const script = document.createElement('script');
    script.src = 'https://yastatic.net/share2/share.js';
    script.async = true;
    document.head.appendChild(script);
  }
});
onBeforeUnmount(() => {
  shouldUpdate.value = false;
});
</script>

<template>

  <B24Slideover
      :title="profile.profileName"
      :description="props.profile.profileName"
      :close="{ onClick: () => { emit('close', false); shouldUpdate.value = false; } }"
      :b24ui="{
     content: 'max-w-[90%] md:max-w-1/2',
     body: 'm-5 p-5 bg-white dark:bg-white/10 rounded'
    }"
  >
    <template #body>
      <div v-if="resultActive" class="mb-4 flex flex-wrap items-center justify-start gap-4">
        <B24Advice
            class="min-w-full"
            :avatar="{ src: '/imges/avatar/assistant.png' }"

        >
          <p class="text-h2">{{ $t('component.profile.activation.resultActive.p1') }}</p>
          <p>{{ $t('component.profile.activation.resultActive.p2') }}</p>
        </B24Advice>
      </div>
      <div v-else >
        <B24Tabs :items="items" variant="link" class="gap-4 w-full" :ui="{ trigger: 'flex-1' }">
          <template #QRactive="{ item }">
            <div class="flex flex-col items-center justify-center">
              <img ref="qrCodeImage" :src="qrCodeSrc" />
            </div>

            <div
                class="flex items-center justify-center gap-2 cursor-pointer hover:text-blue-500"
                @click="copyLinkToClipboard"
            >
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
            </div>
            <div class="mt-4">

            </div>
            <div class="mt-4">
              <div
                  v-html="shareButtonHtml"
              ></div>
            </div>
            <div v-if="showCopied" class="text-green-500 text-sm mt-2">
              Ссылка скопирована в буфер обмена
            </div>
            <p class="text-base-500 dark:text-base-400 mt-4 text-md">
              {{ item.description }}
            </p>
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