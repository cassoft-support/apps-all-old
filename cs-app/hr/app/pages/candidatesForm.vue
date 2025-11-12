<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { initializeB24Frame, B24Frame } from '@bitrix24/b24jssdk'
import {
  getThumbnailSrc,
  formatDate,
  formatDateString,
  formatSubscriptionEndDate,
  authCheck,
} from '@/tools/cs-main';
import { resizeWindow } from '@/tools/bitrix';

export interface ExampleProps {
  color?: ButtonProps['color']
}

withDefaults(defineProps<ExampleProps>(), {
  color: 'primary'
})

const router = useRouter()
const description = ref('Кандидат не привязан')
const candidate = ref<any>(null)
const resume = ref<any>(null)
const candidateProp = ref<Record<string, any>>({})
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

        const candidateId = leadRes._data?.result?.UF_CRM_CS_CANDIDATE;

        if (candidateId && (typeof candidateId === 'string' || typeof candidateId === 'number')) {
          await getCandidate(candidateId)
        } else {
          candidate.value = null
          console.warn('candidateId не указан или недействителен');
        }
      } catch (error) {
        console.error('Ошибка при получении лида:', error);
        candidate.value = null
      }
    }

  } else {
    router.push('/close');
  }

} else {
  router.push('/close');
  console.log('Данные аутентификации истекли или недоступны.');
}

async function getCandidate(candidateId) {
  if (!$b24) {
    console.error('B24Frame не инициализирован');
    return;
  }

  if (!candidateId || typeof candidateId !== 'string' && typeof candidateId !== 'number') {
    console.warn('candidateId недействителен');
    candidate.value = null
    return;
  }

  try {
    const numericCandidateId = Number(candidateId);
    const response = await $b24.callBatch({
      result: {
        method: 'entity.item.get',
        params: {
          ENTITY: 'candidates',
          filter: {
            ACTIVE: 'Y',
            ID: numericCandidateId
          },
          select: ['ID', 'NAME', 'PROPERTY_VALUES']
        }
      }
    }, true)

    if (response.getData().result && response.getData().result.length > 0) {
      candidate.value = response.getData().result[0]
      candidateProp.value = candidate.value.PROPERTY_VALUES
      resume.value = JSON.parse(candidate.value.PROPERTY_VALUES.resume)
    } else {
      candidate.value = null
      console.warn('Кандидат не найден');
    }
  } catch (error) {
    console.error('Ошибка получения данных:', error);
    candidate.value = null
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
    resizeWindow();
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
  <div v-if="candidate" class="p-2 mb-4 flex flex-col justify-between">
    <div class="p-2 mb-4 flex flex-col">
      <div class="mb-2 flex flex-row justify-center">
        <B24Avatar
            v-if="candidateProp.photo"
            :src="candidateProp.photo"
            :alt="`${candidateProp.last_name} ${candidateProp.name} ${candidateProp.second_name}`"
            size="3xl"
        />
        <B24Avatar v-else
                   src="/local/images/avatar/no-av.jpeg"
                   alt="Employee Name"
                   size="3xl"
        />
      </div>
      <div class="mb-2 flex flex-col">
        <div class="text-xs gray-200">ФИО:</div>
        <div class="text-xl gray-950">{{ candidateProp.last_name }} {{ candidateProp.name }} {{ candidateProp.second_name }}</div>
        <div class="text-base gray-950">{{ resume.title }}</div>
      </div>
      <div class="mb-2 flex flex-col">
        <div class="text-xs gray-200">Занятость:</div>
        <div class="text-xl gray-950">{{ resume.employment.name }}</div>
        <div class="text-xl gray-950">{{ resume.business_trip_readiness.name }}</div>
      </div>
      <div v-if="candidateProp.birthdate" class="mb-2 flex flex-col">
        <div class="text-xs gray-200">Дата рождения:</div>
        <div class="text-xl gray-950">{{ formatDateString(candidateProp.birthdate) }}</div>
      </div>
      <div class="mb-2 flex flex-col" v-if="resume.skill_set && resume.skill_set.length > 0">
        <div class="text-xs gray-200">Навыки и компетенции:</div>
        <ul class="list-disc pl-5 mt-1">
          <li v-for="(skill, index) in resume.skill_set" :key="index" class="text-sm">
            {{ skill }}
          </li>
        </ul>
      </div>
    </div>
    <div class="flex flex-row justify-center">
      <B24Button
          class="mt-5"
          :color="color"
          @click="handleClick('candidate', `${candidate.ID}`)"
      >
        Резюме
      </B24Button>
    </div>
  </div>
  <div v-else class="mb-4 flex flex-wrap items-center justify-start gap-4">
    <B24Advice
        angle="top"
        :description="description"
        :icon="{ src: './images/avatar/employee.png' }"
    />
  </div>
</template>
