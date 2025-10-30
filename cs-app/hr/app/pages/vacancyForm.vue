<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
// import { initializeB24Frame, B24Frame } from '@bitrix24/b24jssdk'
// import { authorizationHH, refreshTokenHH, getHhApi } from '@/services/cs_hh'
import {
  getThumbnailSrc,
  formatDate,
  formatDateString,
  formatSubscriptionEndDate,
  authCheck,
} from '@/tools/cs-main';

export interface ExampleProps {
  color?: ButtonProps['color']
}

withDefaults(defineProps<ExampleProps>(), {
  color: 'primary'
})

const router = useRouter()
const description = ref('Вакансия не привязана')
const vacancy = ref<any>(null)
const vacancyProp = ref<Record<string, string>>({})
const currentUser = ref(null)
const {$initializeB24Frame} = useNuxtApp()
const $b24 = await $initializeB24Frame()
const authManager = $b24.auth;
const authData = authManager.getAuthData();

if (authData) {
  const resAuthCheck = await authCheck(authData.member_id, 'hr_pro', 'hr_pro')
  if (resAuthCheck === "Y") {
    if ($b24.placement.options?.ENTITY_ID === 'CRM_LEAD') {
      const entityId = $b24.placement.options.ENTITY_VALUE_ID;
      try {
        const leadRes = await $b24.callMethod('crm.lead.get', { id: entityId });
console.log(leadRes,'leadResVac')
        const vacancyId = leadRes._data?.result?.UF_CRM_CS_VACANCY;

        if (vacancyId && (typeof vacancyId === 'string' || typeof vacancyId === 'number')) {
          await getVacancy(vacancyId)
        } else {
          vacancy.value = null
          console.warn('vacancyId не указан или недействителен');
        }
      } catch (error) {
        console.error('Ошибка при получении лида:', error);
        vacancy.value = null
      }
    }

  } else {
    router.push('/close');
  }

} else {
  router.push('/close');
  console.log('Данные аутентификации истекли или недоступны.');
}

async function getVacancy(vacancyId: string | number) {
  if (!$b24) {
    console.error('B24Frame не инициализирован');
    return;
  }

  if (!vacancyId || typeof vacancyId !== 'string' && typeof vacancyId !== 'number') {
    console.warn('vacancyId недействителен');
    vacancy.value = null
    return;
  }

  try {
    const numericVacancyId = Number(vacancyId);
    const vacancyRes = await $b24.callMethod('entity.item.get', {
      ENTITY: 'ads_control',
      filter: {
        ACTIVE: 'Y',
        ID: numericVacancyId
      }
    });

    if (vacancyRes._data?.result?.length > 0) {
      vacancy.value = vacancyRes._data.result[0];
    } else {
      vacancy.value = null
      console.warn('Вакансия не найдена');
    }
  } catch (error) {
    console.error('Ошибка получения данных:', error);
    vacancy.value = null
  }
}

function handleClick(type: string, id: any) {
  const params = {
    type: type,
    id: id
  };
  $b24.slider.openPath(
      $b24.slider.getUrl(`/marketplace/view/cassoft.hr_pro/?params=${encodeURIComponent(JSON.stringify(params))}`),
      950
  ).then((response) => {
    if (!response.isOpenAtNewWindow && response.isClose) {
      console.log('Slider is closed! Reinit the application');
    }
  });
}

onMounted(async () => {
  try {
  } catch (error) {
    console.error('Ошибка инициализации:', error)
  }
})
</script>
<style>
body{
background: #fff!important;
}
</style>
<template>
  <div v-if="vacancy" class="p-2 mb-2 flex flex-col">
    <div class="mb-2 flex flex-col">
      <div class="text-xs gray-200">Вакансия:</div>
      <h1 class="text-xl gray-950">{{ vacancy.NAME }}</h1>
    </div>
    <div class="mb-2 flex flex-col">
      <div class="text-xs gray-200">Дата публикации на HH:</div>
      <div class="text-xl gray-950">{{ formatDateString(vacancy.PROPERTY_VALUES.hh_date_open) }}</div>
    </div>
    <div class="flex flex-row justify-end">
      <B24Button
          class="mt-5"
          :color="color"
          @click="handleClick('vacancy', `${vacancy.ID}`)"
      >
        Подробнее
      </B24Button>
    </div>
  </div>
  <div v-else class="mb-4 mt-10 flex flex-wrap items-center justify-start gap-4">
    <B24Advice
        angle="top"
        :description="description"
        :icon="{ src: '~/images/avatar/employee.png' }"
    />
  </div>
</template>
