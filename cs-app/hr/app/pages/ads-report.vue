<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { initializeB24Frame, B24Frame } from '@bitrix24/b24jssdk'
import { formatDate } from '@/tools/cs-main'
import {  authorizationHH, refreshTokenHH, getHhApi } from '@/services/cs_hh'
import { useToast, useOverlay } from '#imports'
import { SliderUploadVacancyHH } from '#components'
const overlay = useOverlay()
const sliderUploadVacancyHH = overlay.create(SliderUploadVacancyHH)

async function uploadVacancyHH(userId: string ): Promise<void> {
  await sliderUploadVacancyHH.open({  userId  })
}


definePageMeta({
  layout: 'menu'
})

let $b24: B24Frame | null = null
const vacancy = ref<any[]>([])
const vacancyProp = ref<Record<string, string>>({})
const currentUser = ref(null)


// Форматируем значение для отображения
function formatValue(value: any): string {
  if (typeof value === 'string') {
    // Раскодируем Unicode-символы
    const decoded = value.replace(/\\u[\dA-Fa-f]{4}/g, (match) => {
      return String.fromCharCode(parseInt(match.replace(/\\u/g, ''), 16))
    })
console.log(decoded,'decoded')
    // Если это JSON — парсим
    try {
      const parsed = JSON.parse(decoded)
      console.log(parsed,'parsed')
    //  if (typeof parsed === 'object') {
        return parsed.substring(0, 100) + '...'
    //  }
    } catch (e) {
      // Если это HTML — оставляем как есть
      if (/<[a-zA-Z]/.test(decoded)) {
        return decoded.substring(0, 100) + '...'
      }

      // Если это просто текст — обрезаем
      return decoded.substring(0, 100) + '...'
    }
  }

  return String(value).substring(0, 100) + '...'
}
async function getVacancy() {
  if (!$b24) {
    console.error('B24Frame не инициализирован')
    return
  }

  try {
    // Получаем метаданные полей
    const resProp = await $b24.callMethod('entity.item.property.get', { ENTITY: 'ads_report' })
    const dataProp = resProp.getData().result

    if (Array.isArray(dataProp)) {
      vacancyProp.value = dataProp.reduce((acc, item) => {
        acc[item.PROPERTY] = item.NAME
        return acc
      }, {} as Record<string, string>)
    } else {
      console.warn('Ожидался массив, но пришёл объект:', dataProp)
      vacancyProp.value = {}
    }

    // Получаем данные вакансий
    const response = await $b24.callBatch({
      result: {
        method: 'entity.item.get',
        params: {
          ENTITY: 'ads_report',
          PARAMS: {
            filter: { ACTIVE: 'Y' },
            select: ['ID', 'NAME', 'PROPERTY_VALUES']
          }
        }
      }
    }, true)

    const data = response.getData().result

    if (Array.isArray(data)) {
      vacancy.value = data.map(item => ({
        ID: item.ID,
        NAME: item.NAME,
        PROP: item.PROPERTY_VALUES
      }))
    } else {
      console.warn('Ожидался массив, но пришёл объект:', data)
      vacancy.value = []
    }

    console.log('vacancy:', vacancy.value)
  } catch (error) {
    console.error('Ошибка получения данных:', error)
  }
}

onMounted(async () => {
  try {
    $b24 = await initializeB24Frame()
    const resUser = await $b24.callMethod('user.current')
    currentUser.value = resUser.getData().result
    await getVacancy()
  } catch (error) {
    console.error('Ошибка инициализации:', error)
  }
})
</script>

<style >
.truncate {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>

<template>

  <div class="">Отчет по размещению Вакансий</div>
<!--<div class=""><pre>{{vacancy}}</pre></div>-->
<!--<div class=""><pre>{{formatValue}}</pre></div>-->
  <div class="">Находиться в разработке</div>
</template>