<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue'
import {
  initializeB24Frame,
  LoggerBrowser,
  B24Frame,

} from '@bitrix24/b24jssdk';
definePageMeta({
  layout: 'menu'
})
let $b24: B24Frame;
const responseData = ref<any>('Нет данных');
console.log(responseData.value)
onMounted(async () => {
  try {
    $b24 = await initializeB24Frame();
    console.log('B24Frame инициализирован:', $b24);
  } catch (error) {
    console.error('Ошибка инициализации:', error);
  }
});

async function handleButtonClick() {
  if (!$b24) {
    console.error('B24Frame не инициализирован');
    return;
  }

  try {
    const { execute } = await import('@/tools/apiFunctions'); // Динамический импорт
    const data = await execute($b24);
    console.log('Полученные данные:', data);
    responseData.value = data; // Обновляем реактивную переменную
    console.log('responseData после обновления:', responseData.value);
  } catch (error) {
    console.error('Ошибка при выполнении execute:', error);
    responseData.value = 'Ошибка при загрузке данных'; // Устанавливаем сообщение об ошибке
  }
}

function formatResponseData(data: any): string {
  console.log('Форматируемые данные:', data); // Логирование данных
  if (typeof data === 'string') {
    return data;
  }
  try {
    // Проверяем, есть ли в данных поле result
    if (data && data.result) {
      return JSON.stringify(data.result, null, 2);
    }
    return JSON.stringify(data, null, 2);
  } catch (error) {
    console.error('Ошибка при форматировании данных:', error);
    return 'Ошибка при форматировании данных';
  }
}
</script>

<template>

<B24Container class="mt-12">
<div class="w-full flex flex-col items-center justify-center">
<div class="w-full flex flex-row gap-1 items-center justify-center">
<B24Button
    label="выполнить"
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

</template>