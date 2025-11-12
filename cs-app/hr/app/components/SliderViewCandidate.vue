<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { formatDateString, formatDuration, formatExperienceDate } from '@/tools/cs-main'
import { resizeWindow } from '@/tools/bitrix'
import { useNuxtApp } from '#app'
// Типы данных
interface Candidate {
  ID: number
  NAME: string
  PROPERTY_VALUES: {
    resume: string
    photo?: string
    last_name?: string
    name?: string
    second_name?: string
  }
}

interface Resume {
  last_name?: string
  first_name?: string
  middle_name?: string
  title?: string
  contact?: Array<{
    type: { id: string; name: string }
    value: any
  }>
  experience?: Array<{
    start: string
    end: string
    company: string
    position: string
    description: string
  }>
  education?: {
    primary?: Array<{
      id: string
      name: string
      organization: string
      year: number
      result: string
    }>
  }
  skill_set?: string[]
  citizenship?: Array<{ id: string; name: string }>
  schedule?: { name: string }
  recommendation?: Array<{ id: string; text: string }>
  business_trip_readiness?: { name: string }
  area?: { name: string }
  age?: number
  gender?: { name: string }
  salary?: { amount: number; currency: string }
}

// Реактивные переменные
const candidate = ref<Candidate | null>(null)
const resume = ref<Resume | null>(null)
const candidateProp = ref<Record<string, any>>({})
const currentUser = ref(null)
const elId = ref<string | null>(null)

// Инициализация Bitrix24 Frame
const { $initializeB24Frame } = useNuxtApp()
const $b24 = await $initializeB24Frame()

// Получение данных кандидата
async function getCandidate(candidateId: string) {
  if (!$b24) {
    console.error('B24Frame не инициализирован')
    return
  }

  try {
    const numericCandidateId = Number(candidateId)
    console.log(candidateId, 'candidateId')

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

    const data = response.getData().result[0]
    if (!data) {
      console.error('Данные кандидата не найдены')
      return
    }

    candidate.value = data
    candidateProp.value = data.PROPERTY_VALUES
    resume.value = JSON.parse(data.PROPERTY_VALUES.resume)

    console.log('candidate-resume:', resume.value)
  } catch (error) {
    console.error('Ошибка получения данных:', error)
  }
}

// Инициализация при монтировании
onMounted(async () => {
  try {
    if ($b24 && $b24.placement.options) {
      const options = JSON.parse($b24.placement.options)
      if ($b24.placement.title === "REST_APP_URI" && options.id) {
        elId.value = options.id
        await getCandidate(options.id)
      }
    }

    resizeWindow()
  } catch (error) {
    console.error('Ошибка инициализации:', error)
  }
})
</script>

<template>
  <div class="bg-base-50 w-full h-full pb-8">
    <div class="p-4 mb-4 flex flex-row justify-start">
      <div class="text-h1 font-semibold">Резюме</div>
    </div>

    <B24Container>
      <div class="p-4 mb-4 flex flex-col justify-between bg-white rounded-2xl">
        <div class="p-2 mb-4 flex flex-col">
          <div class=" mb-2 flex flex-row justify-center">
            <div class="w-[240px] h-[240px] rounded-full overflow-hidden">
              <img
                  :src="candidateProp.photo || '/local/images/avatar/no-av.jpeg'"
                  class="object-cover w-full h-full"
              />
            </div>
          </div>
          <div class=" mb-2 flex flex-col">
            <div class="text-xs gray-200">ФИО:</div>
            <div class="text-xl gray-950">
              {{ candidateProp.last_name }} {{ candidateProp.name }} {{ candidateProp.second_name }}
            </div>
            <div class="text-base gray-950">{{ resume?.title || 'Нет данных' }}</div>
          </div>

          <!-- Контактная информация -->
          <div class="mb-4">
            <div class="text-xs text-gray-400">Контакты:</div>
            <ul class="list-disc pl-5">
              <li v-for="contact in resume?.contact || []" :key="contact.type.id">
                <div class="flex flex-row items-center">
                <span class="text-xs text-gray-400 mr-2">{{ contact.type.name }}:</span>

                <!-- Иконка проверки v-if="contact.verified"-->
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                       class="size-4 text-collab-600 dark:text-collab-500 ">
                    <path fill="currentColor" d="M15.822 10.3a.7.7 0 1 0-1.032-.945l-3.536 3.857-2.027-1.737a.7.7 0 0 0-.911 1.063l2.54 2.178a.7.7 0 0 0 .972-.059z"/><path fill="currentColor" fill-rule="evenodd" d="M20 12a8 8 0 1 1-16 0 8 8 0 0 1 16 0m-1.4 0a6.6 6.6 0 1 1-13.2 0 6.6 6.6 0 0 1 13.2 0" clip-rule="evenodd"/></svg>
                <!-- Значение контакта -->
                <span class="text-xl text-gray-900 ml-1">
        <!-- Телефон -->
        <span v-if="contact.type.id === 'cell'">{{ contact.value.formatted }}</span>
                  <!-- Email -->
        <span v-else-if="contact.type.id === 'email'">{{ contact.value }}</span>
     </span>

                <!-- Пометка "предпочитаемый" -->
                <span v-if="contact.preferred" class="text-sm text-gray-500 ml-2">— предпочитаемый способ связи</span>
                </div>
              </li>
            </ul>
          </div>

          <!-- Опыт работы -->
          <div class="mb-4">
            <div class="text-4xl text-gray-400 font-normal mb-2 pr-4">Опыт работы:</div>
            <div class="space-y-4">
              <div v-for="exp in resume?.experience || []" :key="exp.start" class="resume-block-item-gap">
                <div class="flex flex-row gap-2">
                  <!-- Левая колонка: дата -->
                  <div class="flex flex-col w-[25%]">
                    <div class="text-xl text-gray-600">
                      {{ formatExperienceDate(exp.start) }} — {{ exp.end ? formatExperienceDate(exp.end) : 'по настоящее время' }}
                    </div>
                    <div class="text-xs text-gray-400">
                      <span v-if="exp.end">{{ formatDuration(exp.start, exp.end) }}</span>
                    </div>
                  </div>

                  <!-- Правая колонка: компания, должность, описание -->
                  <div class="flex flex-col grow-4">
                    <div class="resume-block-container">
                      <div class="flex flex-row item-start mb-2">
                        <span class="text-xs text-gray-400 mr-2 mt-1">Компания:</span><span class="text-xl text-gray-900 font-semibold"> {{ exp.company }}</span>
                      </div>
                      <div  class="flex flex-row item-start mb-2">
                        <span class="text-xs text-gray-400 mr-2 mt-1">Должность:</span><span class="text-xl text-gray-900"> {{ exp.position }}</span>
                      </div>
                      <div  class="flex flex-row item-start">
                        <span class="text-base text-gray-900"> {{ exp.description }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div v-if="resume?.experience?.length === 0" class="text-gray-500">
                Нет данных об опыте работы
              </div>
            </div>
          </div>

          <!-- Образование -->
          <div class="mb-4">
            <div class="text-4xl text-gray-400 font-normal mb-2 pr-4">Образование:</div>
            <ul class="list-disc pl-5">
              <li v-for="edu in resume?.education?.primary || []" :key="edu.id">
                <span class="text-xs text-gray-400">Название: </span><span class="text-xl text-gray-900"> {{ edu.name }}</span><br>
                <span class="text-xs text-gray-400">Организация: </span><span class="text-xl text-gray-900"> {{ edu.organization }}</span><br>
                <span class="text-xs text-gray-400">Год окончания: </span><span class="text-xl text-gray-900"> {{ edu.year }}</span><br>
                <span class="text-xs text-gray-400">Результат: </span><span class="text-xl text-gray-900"> {{ edu.result }}</span>
              </li>
              <li v-if="resume?.education?.primary?.length === 0">Нет данных об образовании</li>
            </ul>
          </div>

          <!-- Навыки -->
          <div class="mb-4">
            <div class="text-4xl text-gray-400 font-normal mb-2 pr-4">Навыки:</div>
            <ul class="list-disc pl-5">
              <li v-for="skill in resume?.skill_set || []" :key="skill">
                <span class="text-xl text-gray-900"> {{ skill }}</span>
              </li>
              <li v-if="resume?.skill_set?.length === 0">Нет навыков</li>
            </ul>
          </div>

          <!-- Гражданство -->
          <div class="mb-4">
            <div class="text-4xl text-gray-400 font-normal mb-2 pr-4">Гражданство:</div>
            <ul class="list-disc pl-5">
              <li v-for="citizenship in resume?.citizenship || []" :key="citizenship.id">
                <span class="text-xl text-gray-900"> {{ citizenship.name }}</span>
              </li>
              <li v-if="resume?.citizenship?.length === 0">Нет данных о гражданстве</li>
            </ul>
          </div>

          <!-- Расписание -->
          <div class="mb-4">
            <div class="text-4xl text-gray-400 font-normal mb-2 pr-4">Расписание:</div>
            <ul class="list-disc pl-5">
              <li>
                <span class="text-xl text-gray-900"> {{ resume?.schedule?.name || 'Нет данных о расписании' }}</span>
              </li>
            </ul>
          </div>

          <!-- Рекомендации -->
          <div class="mb-4">
            <div class="text-4xl text-gray-400 font-normal mb-2 pr-4">Рекомендации:</div>
            <ul class="list-disc pl-5">
              <li v-for="rec in resume?.recommendation || []" :key="rec.id">
                <span class="text-xl text-gray-900"> {{ rec.text }}</span>
              </li>
              <li v-if="resume?.recommendation?.length === 0">Нет рекомендаций</li>
            </ul>
          </div>

          <!-- Дополнительно -->
          <div class="mb-4">
            <div class="text-4xl text-gray-400 font-normal mb-2 pr-4">Дополнительно:</div>
            <ul class="list-disc pl-5">
              <li>
                <span class="text-xs text-gray-400">Готовность к командировкам: </span>
                <span class="text-xl text-gray-900"> {{ resume?.business_trip_readiness?.name || 'Не готов' }}</span>
              </li>
              <li>
                <span class="text-xs text-gray-400">Расположение: </span>
                <span class="text-xl text-gray-900"> {{ resume?.area?.name || 'Не указано' }}</span>
              </li>
              <li>
                <span class="text-xs text-gray-400">Возраст: </span>
                <span class="text-xl text-gray-900"> {{ resume?.age || 'Не указан' }}</span>
              </li>
              <li>
                <span class="text-xs text-gray-400">Пол: </span>
                <span class="text-xl text-gray-900"> {{ resume?.gender?.name || 'Не указан' }}</span>
              </li>
              <li>
                <span class="text-xs text-gray-400">Зарплата: </span>
                <span class="text-xl text-gray-900"> {{ resume?.salary?.amount }} {{ resume?.salary?.currency || 'Не указана' }}</span>
              </li>
            </ul>
          </div>
          </div>
      </div>
    </B24Container>
  </div>
</template>