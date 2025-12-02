<script setup lang="ts">
import {
  ref,
  onMounted,
  onUnmounted,
  computed,
} from 'vue'
import {
  initializeB24Frame,
  LoggerBrowser,
  B24Frame,
} from '@bitrix24/b24jssdk'

let $b24: B24Frame | null = null
const responseData = ref<any>('Нет данных')
const iframeSrc = ref<string>('https://app.cassoft.ru/local/components/support/mcm/support.php')
const isB24Initialized = ref<boolean>(false)

onMounted(async () => {
  try {
    $b24 = await initializeB24Frame()
    console.log('B24Frame инициализирован:', $b24)
    isB24Initialized.value = true
    if ($b24) {
      const resUser = await $b24.callMethod('user.current');
      const user = resUser.getData().result
      //  console.log(user)
      const response = await $b24.callMethod(
          'imconnector.list',
      );

      const im = response.getData();
      console.log(im, 'im')
    }
  } catch (error) {
    console.error('Ошибка инициализации:', error)
    isB24Initialized.value = false
  }
})

async function handleButtonClick() {
  if (!$b24 || !isB24Initialized.value) {
    console.error('B24Frame не инициализирован')
    return
  }

  try {
    const { execute } = await import('@/tools/apiFunctions')
    const data = await execute($b24)
    console.log('Полученные данные:', data)
    responseData.value = data
  } catch (error) {
    console.error('Ошибка при выполнении execute:', error)
    responseData.value = 'Ошибка при загрузке данных'
  }
}

function formatResponseData(data: any): string {
  if (typeof data === 'string') {
    return data
  }
  try {
    if (data && data.result) {
      return JSON.stringify(data.result, null, 2)
    }
    return JSON.stringify(data, null, 2)
  } catch (error) {
    console.error('Ошибка при форматировании данных:', error)
    return 'Ошибка при форматировании данных'
  }
}
</script>

<template>
  <NuxtLayout name="menu">
    <B24Container class="mt-12">
      <div class="w-full flex flex-col items-center justify-center">
        <div class="w-full flex flex-row gap-1 items-center justify-center">
          <B24Button
              label="Выполнить запрос"
              size="lg"
              rounded
              color="primary"
              loading-auto
              @click.stop="handleButtonClick"
          />
        </div>
      </div>
    </B24Container>

    <B24Container class="mt-12">
      <pre>{{ formatResponseData(responseData) }}</pre>
    </B24Container>

    <B24Container class="mt-12">
<!--      <iframe-->
<!--           ref="iframeRef"-->
<!--           :src="iframeSrc"-->
<!--           class="w-full h-[800px] border border-gray-300 rounded-md"-->
<!--           allow="camera; microphone; fullscreen"-->
<!--           allowfullscreen-->
<!--      ></iframe>-->
    </B24Container>
  </NuxtLayout>
</template>